<?php

namespace Norotaro\FirebaseAuth\Middlewares;

use App;
use Firebase\Auth\Token\Exception\InvalidToken;
use Illuminate\Http\Request;
use Log;
use Winter\User\Models\User;

class AuthenticationControl
{
    protected $auth;

    function __construct(\Kreait\Firebase\Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        try {
            $token = $request->bearerToken();
            $verifiedIdToken = $this->auth->verifyIdToken($token);
            $uid = $verifiedIdToken->claims()->get('sub');

            $user = User::firstWhere('fb_uid', $uid);

            if (!$user) {
                // save user in database
                $fbUser = $this->auth->getUser($uid);
                $datas = UserHelper::instance()->userToArray($fbUser);
                \Db::table('users')->insert($datas);
            }

            \Auth::onceUsingId($user->id);

            return $next($request);
        } catch (InvalidToken $e) {
            Log::error('The token is invalid: ' . $e->getMessage());
            App::abort(403, 'The token is invalid');
        } catch (\InvalidArgumentException $e) {
            Log::error('The token could not be parsed: ' . $e->getMessage());
            App::abort(403, 'The token could not be parsed');
        }

        App::abort(403, 'You are not authenticated');
    }
}
