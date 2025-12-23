<?php

namespace App\Application\Group;

use App\Application\Group\Dto\In\GroupStoreInputDto;
use App\Domain\GroupMember\GroupMemberRepositoryInterface;
use App\Domain\GroupMember\In\GroupMembersStoreInput;
use App\Domain\Group\GroupRepositoryInterface;
use App\Domain\Group\In\GroupStoreInput;
use App\Domain\Group\View\GroupRole;
use App\Domain\Group\View\GroupStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GroupRegisterService {
	public function __construct(
		private GroupRepositoryInterface $groupRepository,
		private GroupMemberRepositoryInterface $groupMemberRepository,
	) {
	}

	public function handle(GroupStoreInputDto $input): void {
		DB::transaction(function () use ($input) {

			$newId = $this->groupRepository->nextId();

			$displayId = $this->generateUniqueDisplayId();

			$groupStore = GroupStoreInput::create(
				id: $newId,
				fkCompanyId: $input->fkCompanyId,
				displayId: $displayId,
				name: $input->name,
				status: GroupStatus::from($input->status),
				description: $input->description
			);

			$groupId = $this->groupRepository->create($groupStore);

			$input = new GroupMembersStoreInput(
				groupId: $groupId,
				memberIds: [$input->actorId],
				actorId: $input->actorId,
				defaultRole: GroupRole::manager(),
			);
			$this->groupMemberRepository->create($input);
		});
	}

	private function generateUniqueDisplayId(): string {
		for ($i = 0; $i < 10; $i++) {
			$displayId = Str::random(8);

			if (!$this->groupRepository->existsByDisplayId($displayId)) {
				return $displayId;
			}
		}

		throw new \RuntimeException(__('group.display_id_exist'));
	}
}
