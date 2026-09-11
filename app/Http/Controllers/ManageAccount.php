<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use App\Models\Employ;
use App\Models\User;

class ManageAccount extends Controller
{
    public function showAccounts():View{
        $employees = Employ::with('roleData')->get();
        return view('manageacc', compact('employees'));
    }

    public function addAccount():View{

    }

    public function resetAccount():view{

    }

    public function deleteAccount():view{

    }
}
