<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $user = auth()->user();

        if (
            $user &&
            $response->isSuccessful() &&
            in_array($request->method(), ['POST', 'PUT', 'DELETE'])
        ) {
            $route_action = $request->route()->getAction();
            list($controller, $action) = explode('@', $route_action['controller']);
            $controller_obj = app()->make($controller);

            if (property_exists($controller_obj, 'label')) {

                $operation = '';
                if ($action == 'store') {
                    $operation = 'Created ';
                }elseif ($action == 'update') {
                    $operation = 'Updated ';
                }elseif ($action == 'destroy') {
                    $operation = 'Deleted ';
                }

                if($operation != ''){
                    $activity = new ActivityLog();

                    if(@$request->operation == 'change-pass'){
                        $label = 'Staff password';
                    }else{
                        $label = $controller_obj->label;
                    }
                    $activity->activity = $operation . $label;
                    $activity->save();
                }
            }
        }

        return $response;
    }
}
