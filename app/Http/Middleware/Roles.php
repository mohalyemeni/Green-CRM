<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Role;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Roles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       $routeName = Route::getFacadeRoot()->current()->uri();
        $route = explode('/',$routeName);
        $roleRoutes = Role::distinct()->whereNotNull('allowed_route')->pluck('allowed_route')->toArray();

        if(auth()->check()){
            if(!in_array($route[0] , $roleRoutes )){
                return $next($request);
            }else{
                if($route[0] != auth()->user()->roles[0]->allowed_route){
                    // $path = $route[0] == auth()->user()->roles[0]->allowed_route ? $route[0].'.login' : '' . auth()->user()->roles[0]->allowed_route . '.index' ;
                    $path = $route[0] == auth()->user()->roles[0]->allowed_route ? $route[0].'.login' : 'frontend' . '.index' ;
                    return redirect()->route($path);
                }else{
                    return $next($request);
                }
            }
        }else{
            $routeDestination = in_array($route[0] , $roleRoutes) ? $route[0]. '.login' : 'login';
            $path = $route[0] != '' ? $routeDestination : auth()->roles[0]->allowed_route.'.index';
            return redirect()->route($path);
        }
    }

    //كود محسن
    // public function handle($request, Closure $next)
    // {
    //     // 1. جلب المقطع الأول من الرابط بطريقة آمنة (مثلاً: admin)
    //     $segment = $request->segment(1);
    //     // 2. جلب المسارات المحمية (يُفضل لاحقاً وضعها في الـ Cache لزيادة السرعة)
    //     $roleRoutes = Role::whereNotNull('allowed_route')->pluck('allowed_route')->toArray();
    //     // 3. إذا كان المسار غير موجود في قائمة المسارات المحمية، دعه يمر
    //     if (!in_array($segment, $roleRoutes)) {
    //         return $next($request);
    //     }
    //     // 4. معالجة المستخدم غير المسجل (الزائر)
    //     if (!auth()->check()) {
    //         // توجيهه لصفحة الدخول الخاصة بالمسار، أو صفحة الدخول الافتراضية
    //         return redirect()->route($segment . '.login');
    //     }
    //     // 5. معالجة المستخدم مسجل الدخول
    //     $user = auth()->user();
    //     $userRole = $user->roles->first(); // نستخدم first لتجنب أخطاء الـ Array
    //     // إذا لم يكن لديه أي دور، نخرجه للواجهة
    //     if (!$userRole) {
    //         return redirect()->route('frontend.index');
    //     }
    //     // 6. إذا كان المسار الحالي يطابق المسار المسموح لدور المستخدم، دعه يمر
    //     if ($segment === $userRole->allowed_route) {
    //         return $next($request);
    //     }
    //     // 7. إذا كان يحاول الدخول لمسار دور آخر، نوجهه للمسار المسموح له هو
    //     if ($userRole->allowed_route) {
    //         return redirect()->route($userRole->allowed_route . '.index');
    //     }
    //     // افتراضياً لأي حالة غير متوقعة
    //     return redirect()->route('frontend.index');
    // }
}
