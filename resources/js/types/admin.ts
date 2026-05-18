export type AdminUser = {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    roles: string[];
    created_at: string;
};

export type AdminRole = {
    id: number;
    name: string;
    permissions: string[];
    users_count: number;
    created_at: string;
};

export type AdminPermission = {
    id: number;
    name: string;
    created_at: string;
};

export type PaginatedData<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    links: PaginationLink[];
};

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};
