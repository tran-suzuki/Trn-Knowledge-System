<?php
namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\DtChatSession;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ChatSessionController extends Controller {
	public function __construct(
	) {}

	public function index(Request $request, DtChatSession $dtChatSession): Response {

		return Inertia::render('Master/Chat/Index', [

		]);
	}

}
