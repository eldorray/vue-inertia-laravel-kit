<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Check, KeyRound, Pencil, Plus, Trash2, X } from 'lucide-vue-next';
import { ref } from 'vue';
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
import type { AdminPermission } from '@/types/admin';

type Props = {
    permissions: AdminPermission[];
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: '/admin' },
            { title: 'Permissions', href: '/admin/permissions' },
        ],
    },
});

const showCreateForm = ref(false);
const editingId = ref<number | null>(null);
const editName = ref('');

const createForm = useForm({
    name: '',
});

function submitCreate() {
    createForm.post('/admin/permissions', {
        onSuccess: () => {
            createForm.reset();
            showCreateForm.value = false;
        },
    });
}

function startEdit(permission: AdminPermission) {
    editingId.value = permission.id;
    editName.value = permission.name;
}

function cancelEdit() {
    editingId.value = null;
    editName.value = '';
}

function submitEdit(permission: AdminPermission) {
    router.patch(
        `/admin/permissions/${permission.id}`,
        { name: editName.value },
        {
            onSuccess: () => {
                editingId.value = null;
                editName.value = '';
            },
        },
    );
}

function deletePermission(permission: AdminPermission) {
    if (
        confirm(
            `Are you sure you want to delete the permission "${permission.name}"?`,
        )
    ) {
        router.delete(`/admin/permissions/${permission.id}`);
    }
}
</script>

<template>
    <Head title="Permissions - Admin" />

    <div class="space-y-6">
        <!-- Page Header -->
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Permissions</h1>
            <p class="text-sm text-muted-foreground">
                Manage system permissions
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                class="rounded-xl border-l-4 border-l-amber-400 bg-background p-5 shadow-sm"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">
                            Total Permissions
                        </p>
                        <p class="mt-1 text-2xl font-bold">
                            {{ permissions.length }}
                        </p>
                    </div>
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100"
                    >
                        <KeyRound class="h-5 w-5 text-amber-600" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions Table Card -->
        <div class="rounded-xl bg-background p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-base font-semibold">All Permissions</h2>
                <Button @click="showCreateForm = !showCreateForm">
                    <Plus class="mr-1 h-4 w-4" /> New Permission
                </Button>
            </div>

            <!-- Create form -->
            <form
                v-if="showCreateForm"
                class="mb-4 flex items-end gap-3 rounded-lg border bg-muted/30 p-4"
                @submit.prevent="submitCreate"
            >
                <div class="flex-1 space-y-1">
                    <label class="text-sm font-medium">Permission Name</label>
                    <Input
                        v-model="createForm.name"
                        placeholder="e.g. posts.create"
                        required
                    />
                    <p
                        v-if="createForm.errors.name"
                        class="text-sm text-destructive"
                    >
                        {{ createForm.errors.name }}
                    </p>
                </div>
                <Button type="submit" :disabled="createForm.processing">
                    Create
                </Button>
                <Button
                    type="button"
                    variant="outline"
                    @click="showCreateForm = false"
                >
                    Cancel
                </Button>
            </form>

            <div class="rounded-lg border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Created</TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="permission in permissions"
                            :key="permission.id"
                        >
                            <TableCell>
                                <div
                                    v-if="editingId === permission.id"
                                    class="flex items-center gap-2"
                                >
                                    <Input
                                        v-model="editName"
                                        class="h-8 max-w-xs"
                                        @keyup.enter="submitEdit(permission)"
                                        @keyup.escape="cancelEdit"
                                    />
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="h-8 w-8"
                                        @click="submitEdit(permission)"
                                    >
                                        <Check class="h-4 w-4" />
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="h-8 w-8"
                                        @click="cancelEdit"
                                    >
                                        <X class="h-4 w-4" />
                                    </Button>
                                </div>
                                <span v-else class="font-medium">
                                    {{ permission.name }}
                                </span>
                            </TableCell>
                            <TableCell class="text-sm text-muted-foreground">
                                {{
                                    new Date(
                                        permission.created_at,
                                    ).toLocaleDateString()
                                }}
                            </TableCell>
                            <TableCell>
                                <div
                                    v-if="editingId !== permission.id"
                                    class="flex items-center gap-1"
                                >
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="h-8 w-8"
                                        @click="startEdit(permission)"
                                    >
                                        <Pencil class="h-4 w-4" />
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="h-8 w-8"
                                        @click="deletePermission(permission)"
                                    >
                                        <Trash2
                                            class="h-4 w-4 text-destructive"
                                        />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="permissions.length === 0">
                            <TableCell
                                colspan="3"
                                class="py-12 text-center text-muted-foreground"
                            >
                                No permissions found.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </div>
</template>
