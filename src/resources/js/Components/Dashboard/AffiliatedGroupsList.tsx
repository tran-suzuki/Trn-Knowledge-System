import React, { useCallback, useEffect, useRef, useState } from 'react';
import { Cursor, Group } from '@/domains/dashboard/dashboard';
import { dashboardRepository } from '@/infrastructure/dashboard/dashboardRepository';
import { mapGroupListDtoToDomain } from '@/infrastructure/dashboard/dashboardMapper';

const LIMIT = 5;
const BOX_HEIGHT_PX = 230;

const AffiliatedGroupsList: React.FC = () => {
	const [groups, setGroups] = useState<Group[]>([]);
	const [nextCursor, setNextCursor] = useState<Cursor>(null);
	const [hasMore, setHasMore] = useState<boolean>(true);

	const [initLoaded, setInitLoaded] = useState<boolean>(false);
	const [error, setError] = useState<string | null>(null);

	const scrollBoxRef = useRef<HTMLDivElement | null>(null);
	const sentinelRef = useRef<HTMLDivElement | null>(null);

	const inFlightRef = useRef(false);
	const lastRequestedCursorRef = useRef<Cursor | undefined>(undefined);

	const [isScrollableReady, setIsScrollableReady] = useState<boolean>(false);

	const autoFillAttemptRef = useRef(0);

	// -----------------------------
	// Fetch
	// -----------------------------
	const fetchGroups = useCallback(
		async ({ cursor = null, append = false }: { cursor?: Cursor; append?: boolean } = {}) => {
			if (inFlightRef.current) return;

			if (lastRequestedCursorRef.current === cursor) return;

			if (append && !hasMore) return;

			inFlightRef.current = true;
			lastRequestedCursorRef.current = cursor;
			setError(null);

			try {
				const res = await dashboardRepository.getGroups(LIMIT, cursor);
				if (res.status) {
					const dashboardGroupListRes = mapGroupListDtoToDomain(res.data);

					const items = Array.isArray(dashboardGroupListRes.groups) ? dashboardGroupListRes.groups : [];
					setGroups((prev) => (append ? [...prev, ...items] : items));
					setNextCursor(dashboardGroupListRes.nextCursor ?? null);
					setHasMore(Boolean(dashboardGroupListRes.hasMore));
				}
			} catch (e: any) {
				lastRequestedCursorRef.current = undefined;
				setError(e?.message ?? 'Fetch failed');
			} finally {
				inFlightRef.current = false;
				setInitLoaded(true);
			}
		},
		[hasMore],
	);

	// -----------------------------
	// 1) initial load
	// -----------------------------
	useEffect(() => {
		fetchGroups({ cursor: null, append: false });
	}, [fetchGroups]);

	// -----------------------------
	// 2) AUTO-FILL (max 1 time)
	//    Ensure the box is scrollable.
	// -----------------------------
	useEffect(() => {
		if (!initLoaded) return;
		if (error) return;

		if (!hasMore || nextCursor == null) {
			setIsScrollableReady(true);
			return;
		}

		const t = window.setTimeout(() => {
			if (inFlightRef.current) return;

			const box = scrollBoxRef.current;
			if (!box) return;

			const hasScroll = box.scrollHeight > box.clientHeight;

			if (hasScroll) {
				setIsScrollableReady(true);
				return;
			}

			if (autoFillAttemptRef.current >= 1) {
				setIsScrollableReady(true);
				return;
			}

			autoFillAttemptRef.current += 1;
			fetchGroups({ cursor: nextCursor, append: true });
		}, 0);

		return () => window.clearTimeout(t);
	}, [groups.length, initLoaded, error, hasMore, nextCursor, fetchGroups]);

	// -----------------------------
	// 3) Infinite scroll in BOX
	// -----------------------------
	useEffect(() => {
		const el = sentinelRef.current;
		const rootEl = scrollBoxRef.current;
		if (!el || !rootEl) return;

		const observer = new IntersectionObserver(
			([entry]) => {
				if (!entry.isIntersecting) return;

				if (!isScrollableReady) return;

				if (!hasMore) return;
				if (nextCursor == null) return;

				fetchGroups({ cursor: nextCursor, append: true });
			},
			{ root: rootEl, rootMargin: '0px', threshold: 0 },
		);

		observer.observe(el);
		return () => observer.disconnect();
	}, [fetchGroups, hasMore, nextCursor, isScrollableReady]);

	// -----------------------------
	// Retry
	// -----------------------------
	const retry = () => {
		lastRequestedCursorRef.current = undefined;
		autoFillAttemptRef.current = 0;
		setIsScrollableReady(false);

		if (!initLoaded || groups.length === 0) {
			fetchGroups({ cursor: null, append: false });
			return;
		}

		if (nextCursor != null) {
			fetchGroups({ cursor: nextCursor, append: true });
			return;
		}

		fetchGroups({ cursor: null, append: false });
	};

	const openChatSession = (displayId: Group['displayId']) => {
		dashboardRepository.goToChat(displayId);
	};

	return (
		<div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
			<h2 className="text-2xl font-bold text-gray-900 mb-4">所属グループ一覧</h2>

			{!initLoaded && <div className="py-6 text-sm text-gray-600">Loading...</div>}

			{error && (
				<div className="p-3 border rounded bg-red-50 mb-4">
					<div className="text-red-700 text-sm">Lỗi: {error}</div>
					<button onClick={retry} className="mt-2 px-3 py-1 border rounded">
						もう一度お試しください。
					</button>
				</div>
			)}

			<div ref={scrollBoxRef} className="overflow-y-auto border rounded" style={{ height: BOX_HEIGHT_PX }}>
				{groups.map((g) => (
					<div key={g.displayId} className="flex justify-between items-center p-4 border-b">
						<div>
							<h4 className="font-semibold">{g.name}</h4>
							<p className="text-sm text-gray-500">
								メンバー {g.memberCount}人 ・ ドキュメント {g.documentCount}件
							</p>
						</div>

						<button
							className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
							onClick={() => openChatSession(g.displayId)}
						>
							AIに尋ねる
						</button>
					</div>
				))}

				{/* sentinel */}
				<div ref={sentinelRef} style={{ height: 1 }} />

				{/* {initLoaded && !error && hasMore && !isScrollableReady && (
          <div className="p-3 text-xs text-gray-400">スクロールを発生させるために自動でデータを補充しています。</div>
        )}
        {initLoaded && !error && !hasMore && (
          <div className="p-3 text-xs text-gray-400">表示するデータがありません。</div>
        )} */}
			</div>
		</div>
	);
};

export default AffiliatedGroupsList;
