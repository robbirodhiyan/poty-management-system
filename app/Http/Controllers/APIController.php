<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Production;
use App\Models\User;

class APIController extends Controller
{
    // Authentication
    public function login(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required',
    ]);
    // dd($request->only('email','password'));
    if (!$token = auth()->attempt($request->only('email', 'password'))) {
      return response()->json([
        'error' => 'Unauthorized',
        'message' => 'Email or Password is incorrect'
      ], 401);
    }


    return response()->json([
      'token' => $token,
      'message' => 'Login Success'
    ], 200);
  }

    public function register(Request $request)
    {
        // Your registration logic here
    }

    // Routes for Owner role

    public function createNote()
    {
        // Logic for creating a note
    }

    public function editNote($id)
    {
        // Logic for editing a note
    }

    public function updateNote($id)
    {
        // Logic for updating a note
    }

    public function deleteNote($id)
    {
        // Logic for deleting a note
    }

    public function index()
    {
        // Logic for dashboard
    }

    public function storeNote(Request $request)
    {
        // Logic for storing a note
    }

    // Add other methods for the 'owner' role...

    public function getTransactionSummary()
    {
        // Logic for getting transaction summary
    }

    public function getPemasukan()
    {
        // Logic for getting pemasukan
    }

    public function storePemasukan(Request $request)
    {
        // Logic for storing pemasukan
    }

    // Add other transaction methods...

    public function getProducts()
    {
        // Logic for getting products
    }

    public function storeProduct(Request $request)
    {
        // Logic for storing a product
    }

    // Add other product methods...

    public function getProductions()
    {
        // Logic for getting productions
    }

    public function storeProduction(Request $request)
    {
        // Logic for storing a production
    }

    // Add other production methods...

    // Routes for Admin role

    public function adminDashboard()
    {
        // Logic for admin dashboard
    }

    // Add other methods for the 'admin' role...

    public function dashboardAnalytics()
    {
        // Logic for main page/dashboard analytics
    }
}
