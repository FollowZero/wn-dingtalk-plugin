<?php namespace Summer\Dingtalk\Classes;

use Db;
use Str;
use Cache;
use Exception;

use AlibabaCloud\SDK\Dingtalk\Voauth2_1_0\Dingtalk;
use AlibabaCloud\Tea\Exception\TeaError;
use AlibabaCloud\Tea\Utils\Utils;

use Darabonba\OpenApi\Models\Config;
use AlibabaCloud\SDK\Dingtalk\Voauth2_1_0\Models\GetAccessTokenRequest;

class AccessToken
{
    protected $app;

    protected $cacheKey='summer-dingtalk-access-token';

    public function __construct($app)
    {
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
    /**
     * accessToken的有效期为7200秒（2小时），有效期内重复获取会返回新的accessToken。
     * 开发者需要缓存accessToken，用于后续接口的调用。因为每个应用的accessToken是彼此独立的，所以进行缓存时需要区分应用来进行存储。
     * 不能频繁调用gettoken接口，否则会受到频率拦截。
     * @param bool|bool $refresh
     * @return array
     */
    public function getToken(bool $refresh = false)
    {
        if (!$refresh && Cache::has($this->cacheKey) && $result = Cache::get($this->cacheKey)) {
            return $result;
        }
        $token=$this->requestToken();
        if($token){
            $this->setToken($token->body->accessToken, $token->body->expireIn ?? 7200);
            return $token->body->accessToken;
        }
    }
    public function requestToken()
    {
        $client = self::createClient();
        $getAccessTokenRequest = new GetAccessTokenRequest([
            "appKey" => $this->app['config']['app_id'],
            "appSecret" => $this->app['config']['secret'],
        ]);
        try {
            $result=$client->getAccessToken($getAccessTokenRequest);
            return $result;
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
    public function setToken(string $token, int $lifetime = 7200)
    {
        Cache::put($this->cacheKey, $token, $lifetime);
        if (!Cache::has($this->cacheKey)) {
            throw new Exception('Failed to cache access token.');
        }
    }

}
