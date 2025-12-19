// types/user/UserListResponseDto.ts
export interface PaginationDto {
	page: number;
	total: number;
	pageSize: number;
}

export interface UserListResponseDto {
	data: any[];
	pagination: PaginationDto;
	filters: any;
	permissions: any;
}
