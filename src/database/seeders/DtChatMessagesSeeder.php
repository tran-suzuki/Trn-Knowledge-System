<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DtChatMessagesSeeder extends Seeder {
	public function run(): void {
		$sessions = DB::table('dt_chat_sessions')
			->select('id', 'fk_group_id')
			->orderBy('id')
			->get();

		$dialogs = [
			[
				['role' => 'user', 'content' => 'お疲れ様です。グループ内の資料を探しています。'],
				['role' => 'model', 'content' => '承知しました。資料の種類（議事録、仕様書など）を教えてください。'],
				['role' => 'user', 'content' => '仕様書の最新版が欲しいです。'],
				['role' => 'model', 'content' => '最新版の候補を検索します。キーワードはありますか？'],
				['role' => 'user', 'content' => '「ユーザー登録」でお願いします。'],
			],
			[
				['role' => 'user', 'content' => 'このグループの最近のチャット履歴はどこで見られますか？'],
				['role' => 'model', 'content' => 'ダッシュボードの「最近のチャット履歴」から確認できます。'],
				['role' => 'user', 'content' => '再開ボタンを押すとどうなりますか？'],
				['role' => 'model', 'content' => '該当セッションのチャット画面に遷移して続きを表示します。'],
				['role' => 'user', 'content' => '了解です。ありがとうございます。'],
			],
			[
				['role' => 'user', 'content' => 'メンバー数が想定より少ない気がします。原因はありますか？'],
				['role' => 'model', 'content' => '削除済みユーザーはカウントから除外されます。'],
				['role' => 'user', 'content' => 'dt_group_user の削除フラグも影響しますか？'],
				['role' => 'model', 'content' => 'はい。dt_group_user.deleted_at がNULLのものだけが有効です。'],
				['role' => 'user', 'content' => '理解しました。確認してみます。'],
			],
			[
				['role' => 'user', 'content' => 'ドキュメント数のカウント条件を教えてください。'],
				['role' => 'model', 'content' => '基本は dt_documents の有効データ（deleted_atがNULL）をカウントします。'],
				['role' => 'user', 'content' => 'type が file のみを対象にできますか？'],
				['role' => 'model', 'content' => '可能です。where type = "file" を条件に追加します。'],
				['role' => 'user', 'content' => 'それでお願いします。'],
			],
			[
				['role' => 'user', 'content' => 'AI尋ねるボタンは何をしますか？'],
				['role' => 'model', 'content' => 'AIグループチャット画面へ遷移します。'],
				['role' => 'user', 'content' => 'セッションタイトルは自動生成ですか？'],
				['role' => 'model', 'content' => 'はい。必要に応じてユーザーが変更できる設計も可能です。'],
				['role' => 'user', 'content' => '今は自動で問題ないです。'],
			],
		];

		foreach ($sessions as $index => $session) {
			$dialog = $dialogs[$index % count($dialogs)];

			// 時刻をずらして「最近順」テストしやすくする
			$baseTime = Carbon::now()->subMinutes(50 - ($index * 10));

			foreach ($dialog as $i => $msg) {
				DB::table('dt_chat_messages')->insert([
					'fk_session_id' => $session->id,
					'role'          => $msg['role'], // user | model
					'content'       => $msg['content'], // 日本語
					'metadata'      => null,
					'created_at'    => $baseTime->copy()->addMinutes($i),
					'updated_at'    => $baseTime->copy()->addMinutes($i),
				]);
			}

			DB::table('dt_chat_sessions')
				->where('id', $session->id)
				->update([
					'updated_at' => $baseTime->copy()->addMinutes(count($dialog) - 1),
				]);
		}
	}
}
