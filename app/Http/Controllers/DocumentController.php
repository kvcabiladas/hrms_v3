<?php
namespace App\Http\Controllers;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller {
    public function index() {
        $documents = Document::latest()->get();
        return view('documents.index', compact('documents'));
    }
}