<script setup>
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone: '',
    position: '',
    department: '',
    bio: '',
})

const submit = () => {
    form.post('/register')
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-8">
        <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
            <h1 class="text-2xl font-bold mb-6 text-center">Register</h1>
            
            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="name">Name</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    />
                    <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="email">Email</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    />
                    <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="password">Password</label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    />
                    <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 mb-2" for="password_confirmation">Confirm Password</label>
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    />
                </div>

                <hr class="my-6 border-gray-300" />
                <h2 class="text-lg font-semibold mb-4 text-gray-700">Profile Information</h2>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="phone">Phone</label>
                    <input
                        id="phone"
                        v-model="form.phone"
                        type="tel"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <div v-if="form.errors.phone" class="text-red-500 text-sm mt-1">{{ form.errors.phone }}</div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="position">Position</label>
                    <input
                        id="position"
                        v-model="form.position"
                        type="text"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <div v-if="form.errors.position" class="text-red-500 text-sm mt-1">{{ form.errors.position }}</div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="department">Department</label>
                    <input
                        id="department"
                        v-model="form.department"
                        type="text"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <div v-if="form.errors.department" class="text-red-500 text-sm mt-1">{{ form.errors.department }}</div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 mb-2" for="bio">Bio</label>
                    <textarea
                        id="bio"
                        v-model="form.bio"
                        rows="3"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    ></textarea>
                    <div v-if="form.errors.bio" class="text-red-500 text-sm mt-1">{{ form.errors.bio }}</div>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 disabled:opacity-50"
                >
                    Register
                </button>
            </form>

            <p class="mt-4 text-center text-gray-600">
                Already have an account?
                <Link href="/login" class="text-blue-500 hover:underline">Login</Link>
            </p>
        </div>
    </div>
</template>
