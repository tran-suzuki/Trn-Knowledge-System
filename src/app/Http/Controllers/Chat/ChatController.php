<?php
namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\MtGroup;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller {
	public function __construct(
	) {}

	public function index(Request $request, MtGroup $mtGroup): Response {

		return Inertia::render('Master/Chat/Index', [

		]);
	}

}
