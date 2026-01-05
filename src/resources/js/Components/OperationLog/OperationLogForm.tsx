import React from 'react';
import { ArrowLeft, Eye, EyeOff } from 'lucide-react';
import { useOperationLogFormStore } from '@/stores/operationLog/operationLogFormStore';
import { operationLogRepository } from '@/infrastructure/operationLog/operationLogRepository';

const OperationLogForm: React.FC = () => {
	const { value } = useOperationLogFormStore();
	console.log(value);
	const handleCancel = () => {
		operationLogRepository.goToList();
	};

	function ViewRow({ label, value }: { label: string; value: React.ReactNode }) {
		return (
			<div>
				<div className="text-sm text-gray-600 mb-1">{label}</div>
				<div className="w-full border rounded px-3 py-2 bg-gray-50 break-words">
					{value ?? <span className="text-gray-400">-</span>}
				</div>
			</div>
		);
	}

	function JsonBox({ value }: { value: unknown }) {
		if (value == null) return <span className="text-gray-400">-</span>;
		return (
			<pre className="w-full border rounded px-3 py-2 bg-gray-50 overflow-auto text-sm whitespace-pre-wrap">
				{JSON.stringify(value, null, 2)}
			</pre>
		);
	}

	return (
		<form className="space-y-4 max-w-xl">
			<div className="grid grid-cols-1 md:grid-cols-2 gap-4">
				<ViewRow label="操作ユーザー名 (WHO)" value={value.name || '-'} />
				<ViewRow label="操作ユーザーメール" value={value.email || '-'} />

				<ViewRow label="操作日時 (WHEN)" value={value.createdDate} />
				<ViewRow label="操作内容 (WHAT)" value={value.action} />

				<ViewRow label="対象テーブル (WHERE)" value={value.targetType || '-'} />
				<ViewRow label="対象レコードID (WHICH)" value={value.targetId || '-'} />

				<ViewRow label="IPアドレス" value={value.ipAddress || '-'} />
				<ViewRow label="ステータス" value={value.detail?.result} />
			</div>

			{/* Detail */}
			<div className="space-y-3">
				<div className="text-base font-semibold">詳細 (detail)</div>

				{/* <ViewRow label="message" value={value.detail?.message ?? '-'} />

				<div>
					<div className="text-sm text-gray-600 mb-1">before</div>
					<JsonBox value={value.detail?.before?.status ?? null} />
				</div>

				<div>
					<div className="text-sm text-gray-600 mb-1">after</div>
					<JsonBox value={value.detail?.after?.status ?? null} />
				</div> */}

				<div>
					<div className="text-sm text-gray-600 mb-1">raw</div>
					<JsonBox value={value.detail} />
				</div>
			</div>

			<div className="flex justify-end space-x-2 pt-4">
				<button type="button" className="px-4 py-2 rounded border border-gray-300 text-gray-700" onClick={handleCancel}>
					<ArrowLeft className="w-5 h-5 inline-block mr-2" />
					戻る
				</button>
			</div>
		</form>
	);
};

export default OperationLogForm;
