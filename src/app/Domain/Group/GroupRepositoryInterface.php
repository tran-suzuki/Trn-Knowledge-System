<?php

namespace App\Domain\Group;

use App\Domain\Dashboard\In\DashboardGroupListInput;
use App\Domain\Group\In\GroupDeleteInput;
use App\Domain\Group\In\GroupListInput;
use App\Domain\Group\In\GroupStoreInput;
use App\Domain\Group\Out\GroupListResult;
use App\Domain\Group\View\Group;

interface GroupRepositoryInterface {
	public function search(GroupListInput $filter): GroupListResult;

	public function getByDisplayId(string $displayId): Group;

	public function delete(GroupDeleteInput $input): void;

	public function nextId(): int;

	public function existsByDisplayId(string $displayId): bool;

	public function create(GroupStoreInput $groupStore): int;

	public function listGroupsForDashboard(DashboardGroupListInput $input): GroupListResult;

}
