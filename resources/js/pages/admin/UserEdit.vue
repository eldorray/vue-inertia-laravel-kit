<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Props = {
    user: {
        id: number;
        name: string;
        email: string;
        roles: string[];
        created_at: string;
    };
    roles: string[];
};

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: '/admin' },
            { title: 'Users', href: '/admin/users' },
            { title: 'Edit', href: '#' },
        ],
    },
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    roles: [...props.user.roles],
});

function toggleRole(role: string) {
    const index = form.roles.indexOf(role);
    if (index === -1) {
        form.roles.push(role);
    } else {
        form.roles.splice(index, 1);
    }
}

function submit() {
    form.patch(`/admin/users/${props.user.id}`);
}
</script>

<template>
    <Head title="Edit User - Admin" />

    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center gap-4">
            <Button variant="ghost" size="icon" class="h-8 w-8" as-child>
                <Link href="/admin/users">
                    <ArrowLeft class="h-4 w-4" />
                </Link>
            </Button>
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Edit User</h1>
                <p class="text-sm text-muted-foreground">
                    Editing {{ user.name }}
                </p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="max-w-2xl rounded-xl bg-background p-6 shadow-sm">
            <form class="space-y-6" @submit.prevent="submit">
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            autofocus
                        />
                        <p
                            v-if="form.errors.name"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                        />
                        <p
                            v-if="form.errors.email"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.email }}
                        </p>
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="password">New Password</Label>
                    <Input
                        id="password"
                        v-model="form.password"
                        type="password"
                        placeholder="Leave blank to keep current password"
                    />
                    <p
                        v-if="form.errors.password"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.password }}
                    </p>
                </div>

                <div class="space-y-3">
                    <Label>Assign Roles</Label>
                    <div
                        class="grid gap-3 rounded-lg border p-4 sm:grid-cols-2"
                    >
                        <div
                            v-for="role in roles"
                            :key="role"
                            class="flex items-center space-x-2"
                        >
                            <Checkbox
                                :id="`role-${role}`"
                                :checked="form.roles.includes(role)"
                                @update:checked="toggleRole(role)"
                            />
                            <Label :for="`role-${role}`" class="font-normal">
                                {{ role }}
                            </Label>
                        </div>
                    </div>
                    <p
                        v-if="form.errors.roles"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.roles }}
                    </p>
                </div>

                <div
                    class="flex items-center gap-3 border-t pt-6"
                >
                    <Button type="submit" :disabled="form.processing">
                        Update User
                    </Button>
                    <Button variant="outline" as-child>
                        <Link href="/admin/users">Cancel</Link>
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
