<?php

namespace App\Application\GroupMember;

use App\Application\GroupMember\Dto\In\GroupMembersStoreInputDto;
use App\Domain\GroupMember\GroupMemberRepositoryInterface;
use App\Domain\GroupMember\In\GroupMembersStoreInput;
use App\Domain\Group\View\GroupRole;
use App\Domain\User\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class GroupMemberStoreService {
	public function __construct(
		private GroupMemberRepositoryInterface $groupMemberRepository,
		private UserRepositoryInterface $memberRepository
	) {}

	public function handle(GroupMembersStoreInputDto $dto) {
		DB::transaction(function () use ($dto) {
			$map = $this->memberRepository->mapIdsByDisplayIds($dto->memberDisplayIds);

			$memberIds = [];
			foreach ($dto->memberDisplayIds as $displayId) {
				if (isset($map[$displayId])) {
					$memberIds[] = (int) $map[$displayId];
				}
			}

			if ($memberIds === []) {
				return;
			}

			$input = new GroupMembersStoreInput(
				groupId: $dto->groupId,
				memberIds: $memberIds,
				actorId: $dto->actorId,
				defaultRole: GroupRole::member(),
			);

			$this->groupMemberRepository->create($input);
		});
	}
}
