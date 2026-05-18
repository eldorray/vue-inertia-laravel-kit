<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Props = {
    permissions: string[];
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: '/admin' },
            { title: 'Roles', href: '/admin/roles' },
            { title: 'Create', href: '/admin/roles/create' },
        ],
    },
});

const form = useForm({
    name: '',
    permissions: [] as string[],
});

function togglePermission(permission: string) {
    const index = form.permissions.indexOf(permission);
    if (index === -1) {
        form.permissions.push(permission);
    } else {
        form.permissions.splice(index, 1);
    }
}

function selectAll() {
    form.permissions = [...(permissions as unknown as string[])];
}

function deselectAll() {
    form.permissions = [];
}

const permissions: string[] = [];

function submit() {
    form.post('/admin/roles');
}
</script>

<template>
    <Head title="Create Role - Admin" />

    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center gap-4">
            <Button variant="ghost" size="icon" class="h-8 w-8" as-child>
                <Link href="/admin/roles">
                    <ArrowLeft class="h-4 w-4" />
                </Link>
            </Button>
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Create Role</h1>
                <p class="text-sm text-muted-foreground">
                    Create a new role with specific permissions
                </p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="max-w-2xl rounded-xl bg-background p-6 shadow-sm">
            <form class="space-y-6" @submit.prevent="submit">
                <div class="space-y-2">
                    <Label for="name">Role Name</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        autofocus
                        placeholder="e.g. editor, moderator"
                    />
                    <p
                        v-if="form.errors.name"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.name }}
                    </p>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <Label>Permissions</Label>
                        <div class="flex gap-2">
                            <Button
                                type="button"
                                variant="ghost"
                                size="sm"
                                class="h-7 text-xs"
                                @click="selectAll"
                            >
                                Select All
                            </Button>
                            <Button
                                type="button"
                                variant="ghost"
                                size="sm"
                                class="h-7 text-xs"
                                @click="deselectAll"
                            >
                                Deselect All
                            </Button>
                        </div>
                    </div>
                    <div
                        class="grid max-h-72 gap-3 overflow-y-auto rounded-lg border p-4 sm:grid-cols-2"
                    >
                        <div
                            v-for="permission in $props.permissions"
                            :key="permission"
                            class="flex items-center space-x-2"
                        >
                            <Checkbox
                                :id="`perm-${permission}`"
                                :checked="form.permissions.includes(permission)"
                                @update:checked="togglePermission(permission)"
                            />
                            <Label
                                :for="`perm-${permission}`"
                                class="font-normal"
                            >
                                {{ permission }}
                            </Label>
                        </div>
                        <p
                            v-if="$props.permissions.length === 0"
                            class="col-span-2 text-sm text-muted-foreground"
                        >
                            No permissions available. Create some first.
                        </p>
                    </div>
                    <p
                        v-if="form.errors.permissions"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.permissions }}
                    </p>
                </div>

                <div class="flex items-center gap-3 border-t pt-6">
                    <Button type="submit" :disabled="form.processing">
                        Create Role
                    </Button>
                    <Button variant="outline" as-child>
                        <Link href="/admin/roles">Cancel</Link>
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
