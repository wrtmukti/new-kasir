<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocsController extends Controller
{
    // ===================== OVERVIEW =====================
    public function index()
    {
        return view('docs.index');
    }

    public function analytics()
    {
        return view('docs.analytics');
    }

    // ===================== UI COMPONENTS =====================
    public function uiComponents()
    {
        return view('docs.ui-components');
    }

    public function components()
    {
        return view('docs.components');
    }

    public function buttons()
    {
        return view('docs.buttons');
    }

    public function cards()
    {
        return view('docs.cards');
    }

    public function forms()
    {
        return view('docs.forms');
    }

    public function tables()
    {
        return view('docs.tables');
    }

    public function charts()
    {
        return view('docs.charts');
    }

    public function icons()
    {
        return view('docs.icons');
    }

    // ===================== MANAGEMENT =====================
    public function users()
    {
        return view('docs.users');
    }

    public function settings()
    {
        return view('docs.settings');
    }

    public function profile()
    {
        return view('docs.profile');
    }

    public function pricing()
    {
        return view('docs.pricing');
    }

    public function kanban()
    {
        return view('docs.kanban');
    }

    public function invoice()
    {
        return view('docs.invoice');
    }

    public function blank()
    {
        return view('docs.blank');
    }

    // ===================== RESOURCES =====================
    public function documentation()
    {
        return view('docs.documentation');
    }

    public function authLogin()
    {
        return view('docs.auth-login');
    }

    // ===================== ERROR =====================
    public function error404()
    {
        return view('docs.error-404');
    }
}
