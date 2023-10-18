<?php namespace Summer\Dingtalk\Classes;

use Carbon\Carbon;
use Db;
use Str;
use App;
use Cache;
use Exception;

use AlibabaCloud\SDK\Dingtalk\Vrobot_1_0\Dingtalk;
use AlibabaCloud\Tea\Exception\TeaError;
use AlibabaCloud\Tea\Utils\Utils;

use Darabonba\OpenApi\Models\Config;
use AlibabaCloud\SDK\Dingtalk\Vrobot_1_0\Models\OrgGroupSendHeaders;
use AlibabaCloud\SDK\Dingtalk\Vrobot_1_0\Models\OrgGroupSendRequest;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use Summer\Dingtalk\Models\DingtalkTemplateModel;
use Summer\Dingtalk\Models\Settings;


class Robot
{
    protected $app;

    public function __construct()
    {
        $config=Settings::instance()->toArray();
        $app['config']=$config;
        if(!isset($app['access_token'])){
            $AccessToken =new AccessToken($app);
            $app['access_token']=$AccessToken->getToken();
        }
        $this->app = $app;
    }
    /**
     * 使用 Token 初始化账号Client
     * @return Dingtalk Client
     */
    public static function createClient(){
        $config = new Config([]);
        $config->protocol = "https";
        $config->regionId = "central";
        return new Dingtalk($config);
    }

    public function groupMessagesSend($msg){
        $msgTemplate=$this->getMsgTemplate($msg);
        $client = self::createClient();
        $orgGroupSendHeaders = new OrgGroupSendHeaders([]);
        $orgGroupSendHeaders->xAcsDingtalkAccessToken = $this->app['access_token'];
        $orgGroupSendRequest = new OrgGroupSendRequest([
            "msgParam" => $msgTemplate['msgParam'],
            "msgKey" => $msgTemplate['msgKey'],
            "openConversationId" => $this->app['config']['open_conversation_id'],
            "robotCode" => $this->app['config']['robot_code'],
        ]);
        try {
            $client->orgGroupSendWithOptions($orgGroupSendRequest, $orgGroupSendHeaders, new RuntimeOptions([]));
        }
        catch (Exception $err) {

            if (!($err instanceof TeaError)) {
                $err = new TeaError([], $err->getMessage(), $err->getCode(), $err);
            }
            if (!Utils::empty_($err->code) && !Utils::empty_($err->message)) {
                // err 中含有 code 和 message 属性，可帮助开发定位问题
            }
        }
    }

    public function getMsgTemplate($msg){
        $msgTemplate=[];
        $template_param=$msg['template_param']??[];
        $template=DingtalkTemplateModel::where('code',$msg['template_code'])->first();
        if($template){
            $msgKey=$template->type;
            // 构建消息内容
            $msgParam=[];
            switch ($msgKey) {
                case 'sampleText':
                    $text = $this->renderText($template->content_text, $template_param);
                    $msgParam['content']=$text;
                    break;
                case 'sampleMarkdown':
                    $text = $this->renderText($template->content_md, $template_param);
                    $msgParam['title']=$template->title;
                    $msgParam['text']=$text;
                    break;
                case 'sampleLink':
                    $text = $this->renderText($template->content_text,$template_param);
                    $picUrl=$msg['template_param']['picUrl']??'';
                    if(!$picUrl){
                        $picUrl=$template->pic?$template->pic->path:'';
                    }
                    $msgParam['title']=$template->title;
                    $msgParam['text']=$text;
                    $msgParam['picUrl']=$picUrl;
                    $msgParam['messageUrl']=$msg['template_param']['messageUrl']??$template->message_url;
                    break;
                case 'sampleImageMsg':
                    $photoURL=$msg['template_param']['photoURL']??'';
                    if(!$photoURL){
                        $photoURL=$template->photo?$template->photo->path:'';
                    }
                    $msgParam['photoURL']=$photoURL;
                    break;
                case 'sampleActionCard':
                    $text = $this->renderText($template->content_text,$template_param);
                    $msgParam['title']=$template->title;
                    $msgParam['text']=$text;
                    $button_count=count($template->action_card);
                    if($button_count==1){
                        $msgParam['singleTitle']=$template->action_card[0]['title'];
                        $msgParam['singleURL']=$template->action_card[0]['url'];
                    }elseif($button_count==2){
                        //判断横向或纵向
                        $button_type=$template->button_type;
                        if($button_type==1){
                            //横向按钮
                            foreach ($template->action_card as $key=> $action_card){
                                $msgParam['buttonTitle'.$key+1]=$action_card['title'];
                                $msgParam['buttonUrl'.$key+1]=$action_card['url'];
                            }
                            $msgKey='sampleActionCard6';
                        }else{
                            foreach ($template->action_card as $key=> $action_card){
                                $msgParam['actionTitle'.$key+1]=$action_card['title'];
                                $msgParam['actionURL'.$key+1]=$action_card['url'];
                            }
                            $msgKey='sampleActionCard'.$button_count;
                        }
                    }else{
                        foreach ($template->action_card as $key=> $action_card){
                            $msgParam['actionTitle'.$key+1]=$action_card['title'];
                            $msgParam['actionURL'.$key+1]=$action_card['url'];
                        }
                        $msgKey='sampleActionCard'.$button_count;
                    }
                    break;
                default:
                    throw new Exception('消息类型获取失败.');
            }
            $msgTemplate['msgKey']=$msgKey;
            $msgTemplate['msgParam']=json_encode($msgParam);
        }else{
            throw new Exception('信息模版不存在.');
        }
        return $msgTemplate;
    }

    public function renderText($content, $data = [])
    {
        if (!$content) {
            return '';
        }
        $text = $this->renderTwig($content, $data);
        $text = html_entity_decode(preg_replace("/[\r\n]{2,}/", "\n\n", $text), ENT_QUOTES, 'UTF-8');
        return $text;
    }
    protected function renderTwig(string $content, array $data = []): string
    {
        return App::make('twig.environment.mailer')
            ->createTemplate($content)
            ->render($data);
    }
}
