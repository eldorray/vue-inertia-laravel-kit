<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import {
    Check,
    GripVertical,
    Menu,
    Pencil,
    Plus,
    Trash2,
    X,
} from 'lucide-vue-next';
import { ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

type MenuItem = {
    id: number;
    title: string;
    url: string;
    icon: string;
    order: number;
    is_active: boolean;
    roles: string[];
    created_at: string;
};

type Props = {
    menus: MenuItem[];
    roles: string[];
};

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: '/admin' },
            { title: 'Menus', href: '/admin/menus' },
        ],
    },
});

// Create form
const showCreateForm = ref(false);
const createForm = useForm({
    title: '',
    url: '',
    icon: 'Circle',
    order: 0,
    is_active: true,
    roles: [] as string[],
});

function toggleCreateRole(role: string) {
    const index = createForm.roles.indexOf(role);
    if (index === -1) {
        createForm.roles.push(role);
    } else {
        createForm.roles.splice(index, 1);
    }
}

function submitCreate() {
    createForm.post('/admin/menus', {
        onSuccess: () => {
            createForm.reset();
            showCreateForm.value = false;
        },
    });
}

// Edit form
const editingId = ref<number | null>(null);
const editForm = useForm({
    title: '',
    url: '',
    icon: '',
    order: 0,
    is_active: true,
    roles: [] as string[],
});

function startEdit(menu: MenuItem) {
    editingId.value = menu.id;
    editForm.title = menu.title;
    editForm.url = menu.url;
    editForm.icon = menu.icon;
    editForm.order = menu.order;
    editForm.is_active = menu.is_active;
    editForm.roles = [...menu.roles];
}

function cancelEdit() {
    editingId.value = null;
    editForm.reset();
}

function toggleEditRole(role: string) {
    const index = editForm.roles.indexOf(role);
    if (index === -1) {
        editForm.roles.push(role);
    } else {
        editForm.roles.splice(index, 1);
    }
}

function submitEdit(menu: MenuItem) {
    editForm.patch(`/admin/menus/${menu.id}`, {
        onSuccess: () => {
            editingId.value = null;
            editForm.reset();
        },
    });
}

function deleteMenu(menu: MenuItem) {
    if (confirm(`Are you sure you want to delete "${menu.title}"?`)) {
        router.delete(`/admin/menus/${menu.id}`);
    }
}

function toggleActive(menu: MenuItem) {
    router.patch(`/admin/menus/${menu.id}`, {
        title: menu.title,
        url: menu.url,
        icon: menu.icon,
        order: menu.order,
        is_active: !menu.is_active,
        roles: menu.roles,
    });
}
</script>

<template>
    <Head title="Menus - Admin" />

    <div class="space-y-6">
        <!-- Page Header -->
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Menus</h1>
            <p class="text-sm text-muted-foreground">
                Manage sidebar navigation menus and assign them to roles
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                class="rounded-xl border-l-4 border-l-blue-400 bg-background p-5 shadow-sm"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">
                            Total Menus
                        </p>
                        <p class="mt-1 text-2xl font-bold">
                            {{ menus.length }}
                        </p>
                    </div>
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100"
                    >
                        <Menu class="h-5 w-5 text-blue-600" />
                    </div>
                </div>
            </div>
            <div
                class="rounded-xl border-l-4 border-l-green-400 bg-background p-5 shadow-sm"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">
                            Active
                        </p>
                        <p class="mt-1 text-2xl font-bold">
                            {{ menus.filter((m) => m.is_active).length }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menus Table Card -->
        <div class="rounded-xl bg-background p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-base font-semibold">All Menus</h2>
                <Button @click="showCreateForm = !showCreateForm">
                    <Plus class="mr-1 h-4 w-4" /> New Menu
                </Button>
            </div>

            <!-- Create Form -->
            <div
                v-if="showCreateForm"
                class="mb-6 rounded-lg border bg-muted/30 p-5"
            >
                <h3 class="mb-4 text-sm font-semibold">Add New Menu</h3>
                <form class="space-y-4" @submit.prevent="submitCreate">
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="space-y-1">
                            <Label for="create-title">Title</Label>
                            <Input
                                id="create-title"
                                v-model="createForm.title"
                                placeholder="Menu title"
                                required
                            />
                            <p
                                v-if="createForm.errors.title"
                                class="text-xs text-destructive"
                            >
                                {{ createForm.errors.title }}
                            </p>
                        </div>
                        <div class="space-y-1">
                            <Label for="create-url">URL</Label>
                            <Input
                                id="create-url"
                                v-model="createForm.url"
                                placeholder="/path/to/page"
                                required
                            />
                            <p
                                v-if="createForm.errors.url"
                                class="text-xs text-destructive"
                            >
                                {{ createForm.errors.url }}
                            </p>
                        </div>
                        <div class="space-y-1">
                            <Label for="create-icon">Icon (Lucide name)</Label>
                            <Input
                                id="create-icon"
                                v-model="createForm.icon"
                                placeholder="e.g. FileText, Users"
                            />
                            <p
                                v-if="createForm.errors.icon"
                                class="text-xs text-destructive"
                            >
                                {{ createForm.errors.icon }}
                            </p>
                        </div>
                        <div class="space-y-1">
                            <Label for="create-order">Order</Label>
                            <Input
                                id="create-order"
                                v-model.number="createForm.order"
                                type="number"
                                min="0"
                            />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label>Visible to Roles (empty = visible to all)</Label>
                        <div class="flex flex-wrap gap-3">
                            <div
                                v-for="role in roles"
                                :key="role"
                                class="flex items-center space-x-2"
                            >
                                <Checkbox
                                    :id="`create-role-${role}`"
                                    :checked="createForm.roles.includes(role)"
                                    @update:checked="toggleCreateRole(role)"
                                />
                                <Label
                                    :for="`create-role-${role}`"
                                    class="font-normal"
                                >
                                    {{ role }}
                                </Label>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <Button
                            type="submit"
                            :disabled="createForm.processing"
                        >
                            Create Menu
                        </Button>
                        <Button
                            type="button"
                            variant="outline"
                            @click="showCreateForm = false"
                        >
                            Cancel
                        </Button>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="rounded-lg border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[50px]">Order</TableHead>
                            <TableHead>Title</TableHead>
                            <TableHead>URL</TableHead>
                            <TableHead>Icon</TableHead>
                            <TableHead>Roles</TableHead>
                            <TableHead>Active</TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="menu in menus" :key="menu.id">
                            <!-- Editing mode -->
                            <template v-if="editingId === menu.id">
                                <TableCell>
                                    <Input
                                        v-model.number="editForm.order"
                                        type="number"
                                        min="0"
                                        class="h-8 w-16"
                                    />
                                </TableCell>
                                <TableCell>
                                    <Input
                                        v-model="editForm.title"
                                        class="h-8"
                                    />
                                </TableCell>
                                <TableCell>
                                    <Input
                                        v-model="editForm.url"
                                        class="h-8"
                                    />
                                </TableCell>
                                <TableCell>
                                    <Input
                                        v-model="editForm.icon"
                                        class="h-8 w-28"
                                    />
                                </TableCell>
                                <TableCell>
                                    <div class="flex flex-wrap gap-1">
                                        <div
                                            v-for="role in roles"
                                            :key="role"
                                            class="flex items-center space-x-1"
                                        >
                                            <Checkbox
                                                :id="`edit-role-${role}`"
                                                :checked="
                                                    editForm.roles.includes(
                                                        role,
                                                    )
                                                "
                                                class="h-3.5 w-3.5"
                                                @update:checked="
                                                    toggleEditRole(role)
                                                "
                                            />
                                            <label
                                                :for="`edit-role-${role}`"
                                                class="text-xs"
                                            >
                                                {{ role }}
                                            </label>
                                        </div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <Checkbox
                                        :checked="editForm.is_active"
                                        @update:checked="
                                            editForm.is_active =
                                                !editForm.is_active
                                        "
                                    />
                                </TableCell>
                                <TableCell>
                                    <div class="flex items-center gap-1">
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="h-8 w-8"
                                            @click="submitEdit(menu)"
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
                                </TableCell>
                            </template>

                            <!-- Display mode -->
                            <template v-else>
                                <TableCell>
                                    <span
                                        class="flex items-center gap-1 text-muted-foreground"
                                    >
                                        <GripVertical class="h-3 w-3" />
                                        {{ menu.order }}
                                    </span>
                                </TableCell>
                                <TableCell class="font-medium">
                                    {{ menu.title }}
                                </TableCell>
                                <TableCell class="text-muted-foreground">
                                    {{ menu.url }}
                                </TableCell>
                                <TableCell>
                                    <Badge variant="outline" class="rounded-md">
                                        {{ menu.icon }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <div class="flex flex-wrap gap-1">
                                        <Badge
                                            v-for="role in menu.roles"
                                            :key="role"
                                            variant="secondary"
                                            class="rounded-md"
                                        >
                                            {{ role }}
                                        </Badge>
                                        <span
                                            v-if="menu.roles.length === 0"
                                            class="text-xs text-muted-foreground"
                                        >
                                            All users
                                        </span>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <Badge
                                        :variant="
                                            menu.is_active
                                                ? 'default'
                                                : 'secondary'
                                        "
                                        class="cursor-pointer rounded-md"
                                        @click="toggleActive(menu)"
                                    >
                                        {{ menu.is_active ? 'Active' : 'Inactive' }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <div class="flex items-center gap-1">
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="h-8 w-8"
                                            @click="startEdit(menu)"
                                        >
                                            <Pencil class="h-4 w-4" />
                                        </Button>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="h-8 w-8"
                                            @click="deleteMenu(menu)"
                                        >
                                            <Trash2
                                                class="h-4 w-4 text-destructive"
                                            />
                                        </Button>
                                    </div>
                                </TableCell>
                            </template>
                        </TableRow>
                        <TableRow v-if="menus.length === 0">
                            <TableCell
                                colspan="7"
                                class="py-12 text-center text-muted-foreground"
                            >
                                No menus yet. Create one to get started.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <p class="mt-3 text-xs text-muted-foreground">
                💡 Tip: Icon names are from
                <a
                    href="https://lucide.dev/icons"
                    target="_blank"
                    class="underline"
                    >Lucide Icons</a
                >. Use PascalCase (e.g. FileText, Users, ShoppingCart).
                If roles are empty, the menu is visible to all authenticated users.
            </p>
        </div>
    </div>
</template>
