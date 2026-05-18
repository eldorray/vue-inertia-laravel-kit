<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Shield, Trash2 } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { AdminRole } from '@/types/admin';

type Props = {
    roles: AdminRole[];
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: '/admin' },
            { title: 'Roles', href: '/admin/roles' },
        ],
    },
});

function deleteRole(role: AdminRole) {
    if (confirm(`Are you sure you want to delete the role "${role.name}"?`)) {
        router.delete(`/admin/roles/${role.id}`);
    }
}
</script>

<template>
    <Head title="Roles - Admin" />

    <div class="space-y-6">
        <!-- Page Header -->
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Roles</h1>
            <p class="text-sm text-muted-foreground">
                Manage roles and their permissions
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                class="rounded-xl border-l-4 border-l-emerald-400 bg-background p-5 shadow-sm"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">
                            Total Roles
                        </p>
                        <p class="mt-1 text-2xl font-bold">
                            {{ roles.length }}
                        </p>
                    </div>
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100"
                    >
                        <Shield class="h-5 w-5 text-emerald-600" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Roles Table Card -->
        <div class="rounded-xl bg-background p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-base font-semibold">All Roles</h2>
                <Button as-child>
                    <Link href="/admin/roles/create">
                        <Plus class="mr-1 h-4 w-4" /> New Role
                    </Link>
                </Button>
            </div>

            <div class="rounded-lg border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Permissions</TableHead>
                            <TableHead>Users</TableHead>
                            <TableHead>Created</TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="role in roles" :key="role.id">
                            <TableCell class="font-medium">
                                {{ role.name }}
                            </TableCell>
                            <TableCell>
                                <div class="flex flex-wrap gap-1">
                                    <Badge
                                        v-for="permission in role.permissions.slice(0, 3)"
                                        :key="permission"
                                        variant="outline"
                                        class="rounded-md"
                                    >
                                        {{ permission }}
                                    </Badge>
                                    <Badge
                                        v-if="role.permissions.length > 3"
                                        variant="secondary"
                                        class="rounded-md"
                                    >
                                        +{{ role.permissions.length - 3 }} more
                                    </Badge>
                                    <span
                                        v-if="role.permissions.length === 0"
                                        class="text-sm text-muted-foreground"
                                    >
                                        —
                                    </span>
                                </div>
                            </TableCell>
                            <TableCell>
                                <Badge variant="secondary" class="rounded-md">
                                    {{ role.users_count }} users
                                </Badge>
                            </TableCell>
                            <TableCell class="text-sm text-muted-foreground">
                                {{
                                    new Date(
                                        role.created_at,
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
                                            :href="`/admin/roles/${role.id}/edit`"
                                        >
                                            <Pencil class="h-4 w-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="h-8 w-8"
                                        :disabled="role.name === 'super-admin'"
                                        @click="deleteRole(role)"
                                    >
                                        <Trash2
                                            class="h-4 w-4 text-destructive"
                                        />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="roles.length === 0">
                            <TableCell
                                colspan="5"
                                class="py-12 text-center text-muted-foreground"
                            >
                                No roles found.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </div>
</template>
