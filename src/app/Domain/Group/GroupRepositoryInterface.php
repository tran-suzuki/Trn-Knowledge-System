<?php

namespace App\Domain\Group;

use App\Domain\Group\In\GroupListForDashboardInput;
use App\Domain\Group\In\GroupListInput;
use App\Domain\Group\Out\GroupListForDashboardResult;
use App\Domain\Group\Out\GroupListResult;
use App\Domain\Group\View\Group;

interface GroupRepositoryInterface {
	public function search(GroupListInput $filter): GroupListResult;

	public function nextId(): int;

	public function existsByDisplayId(string $displayId): bool;

	public function create(Group $groupStore): int;

	public function getById(string $groupId): Group;

	public function getByDisplayId(string $displayId): Group;

	public function delete(Group $group): void;

	public function listGroupsForDashboard(GroupListForDashboardInput $input): GroupListForDashboardResult;

}
