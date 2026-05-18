<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Search, Trash2, Users } from 'lucide-vue-next';
import { ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { PaginatedData, AdminUser } from '@/types/admin';

type Props = {
    users: PaginatedData<AdminUser>;
    filters: {
        search: string | null;
    };
};

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: '/admin' },
            { title: 'Users', href: '/admin/users' },
        ],
    },
});

const search = ref(props.filters.search ?? '');

function handleSearch() {
    router.get(
        '/admin/users',
        { search: search.value || undefined },
        { preserveState: true, replace: true },
    );
}

function deleteUser(user: AdminUser) {
    if (confirm(`Are you sure you want to delete "${user.name}"?`)) {
        router.delete(`/admin/users/${user.id}`);
    }
}
</script>

<template>
    <Head title="Users - Admin" />

    <div class="space-y-6">
        <!-- Page Header -->
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Users</h1>
            <p class="text-sm text-muted-foreground">
                Manage all users in the system
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                class="rounded-xl border-l-4 border-l-violet-400 bg-background p-5 shadow-sm"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">
                            Total Users
                        </p>
                        <p class="mt-1 text-2xl font-bold">
                            {{ users.total }}
                        </p>
                    </div>
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-violet-100"
                    >
                        <Users class="h-5 w-5 text-violet-600" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table Card -->
        <div class="rounded-xl bg-background p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between gap-4">
                <form
                    class="relative flex-1"
                    @submit.prevent="handleSearch"
                >
                    <Search
                        class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="search"
                        placeholder="Search users by name or email..."
                        class="max-w-sm pl-9"
                        @keyup.enter="handleSearch"
                    />
                </form>

                <Button as-child>
                    <Link href="/admin/users/create">
                        <Plus class="mr-1 h-4 w-4" /> New User
                    </Link>
                </Button>
            </div>

            <div class="rounded-lg border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Roles</TableHead>
                            <TableHead>Created</TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="user in users.data" :key="user.id">
                            <TableCell class="font-medium">
                                {{ user.name }}
                            </TableCell>
                            <TableCell class="text-muted-foreground">
                                {{ user.email }}
                            </TableCell>
                            <TableCell>
                                <div class="flex flex-wrap gap-1">
                                    <Badge
                                        v-for="role in user.roles"
                                        :key="role"
                                        variant="secondary"
                                        class="rounded-md"
                                    >
                                        {{ role }}
                                    </Badge>
                                    <span
                                        v-if="user.roles.length === 0"
                                        class="text-sm text-muted-foreground"
                                    >
                                        —
                                    </span>
                                </div>
                            </TableCell>
                            <TableCell class="text-sm text-muted-foreground">
                                {{
                                    new Date(
                                        user.created_at,
                                    ).toLocaleDateString()
                                }}
                            </TableCell>
                            <TableCell>
                                <div class="flex items-center gap-1">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="h-8 w-8"
                                        as-child
                                    >
                                        <Link
                                            :href="`/admin/users/${user.id}/edit`"
                                        >
                                            <Pencil class="h-4 w-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="h-8 w-8"
                                        @click="deleteUser(user)"
                                    >
                                        <Trash2
                                            class="h-4 w-4 text-destructive"
                                        />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="users.data.length === 0">
                            <TableCell
                                colspan="5"
                                class="py-12 text-center text-muted-foreground"
                            >
                                No users found.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <div
                v-if="users.last_page > 1"
                class="mt-4 flex items-center justify-between"
            >
                <p class="text-sm text-muted-foreground">
                    Showing {{ users.from }} to {{ users.to }} of
                    {{ users.total }} users
                </p>
                <div class="flex gap-1">
                    <Button
                        v-for="link in users.links"
                        :key="link.label"
                        variant="outline"
                        size="sm"
                        class="h-8"
                        :disabled="!link.url || link.active"
                        as-child
                    >
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            preserve-state
                            v-html="link.label"
                        />
                        <span v-else v-html="link.label" />
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
