<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
		$user = Auth::user();
		$intent = $user->createSetupIntent();
		return view('home', compact('intent'));
    }
	
	public function subscribe(Request $request)
    {
		$stripe_plan="plan_FrSBmwjPMDuNOZ";
		
		$user = $request->user();
		$paymentMethod = $request->paymentMethod;

		$user->createOrGetStripeCustomer();
		$user->updateDefaultPaymentMethod($paymentMethod);
		$user
			->newSubscription('main', $stripe_plan)
			->create($paymentMethod, [
				'email' => $user->email,
			]);
		return redirect()->route('home')->with('success', 'Your plan subscribed successfully');
    }
}
