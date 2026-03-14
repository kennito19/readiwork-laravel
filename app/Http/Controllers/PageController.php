<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function services()
    {
        $prices = [
            'identity-verification'   => Setting::servicePrice('identity-verification'),
            'credit-score-check'      => Setting::servicePrice('credit-score-check'),
            'crb-blacklist-check'     => Setting::servicePrice('crb-blacklist-check'),
            'full-credit-report'      => Setting::servicePrice('full-credit-report'),
            'loan-eligibility'        => Setting::servicePrice('loan-eligibility'),
            'identity-scrub'          => Setting::servicePrice('identity-scrub'),
            'full-json-credit-report' => Setting::servicePrice('full-json-credit-report'),
            'enhanced-credit-info'    => Setting::servicePrice('enhanced-credit-info'),
            'credit-info-income'      => Setting::servicePrice('credit-info-income'),
            'full-enhanced-credit'    => Setting::servicePrice('full-enhanced-credit'),
        ];
        return view('services', compact('prices'));
    }

    public function pricing()
    {
        $prices = [
            'identity-verification' => Setting::servicePrice('identity-verification'),
            'credit-score-check'    => Setting::servicePrice('credit-score-check'),
            'crb-blacklist-check'   => Setting::servicePrice('crb-blacklist-check'),
            'full-credit-report'    => Setting::servicePrice('full-credit-report'),
            'loan-eligibility'      => Setting::servicePrice('loan-eligibility'),
        ];
        return view('pricing', compact('prices'));
    }
    public function about()      { return view('about'); }
    public function faq()        { return view('faq'); }
    public function business()   { return view('business'); }
    public function getStarted()
    {
        $prices = [
            'identity-verification' => Setting::servicePrice('identity-verification'),
            'credit-score-check'    => Setting::servicePrice('credit-score-check'),
            'crb-blacklist-check'   => Setting::servicePrice('crb-blacklist-check'),
            'loan-eligibility'      => Setting::servicePrice('loan-eligibility'),
            'full-credit-report'    => Setting::servicePrice('full-credit-report'),
        ];
        return view('get-started', compact('prices'));
    }
    public function privacy()    { return view('privacy'); }
    public function terms()      { return view('terms'); }
    public function cookies()    { return view('cookies'); }
    public function documentation() { return view('documentation'); }

    public function contact()
    {
        return view('contact');
    }

    public function contactSubmit(Request $request)
    {
        // Contact form — just redirect back with success for now
        return back()->with('success', 'Your message has been sent. We\'ll get back to you soon!');
    }
}
