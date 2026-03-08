<script setup>
import { ref } from 'vue'
import { useForm, usePage, router } from '@inertiajs/vue3'

const page = usePage()
const user = page.props.auth.user
const isTechnician = page.props.isTechnician

const activeTab = ref('welcome')

const logoutForm = useForm({})
const logout = () => {
    logoutForm.post('/logout')
}

const selectedTicket = ref(null)
const ticketDetail = ref(null)
const loadingDetail = ref(false)
const showCreateForm = ref(false)

const ticketForm = useForm({
    title: '',
    description: '',
    priority: 'medium',
    project_id: '',
    details_file: null,
})

const onFileChange = (e) => {
    ticketForm.details_file = e.target.files[0]
}

const submitTicket = () => {
    ticketForm.post('/tickets', {
        forceFormData: true,
        onSuccess: () => {
            showCreateForm.value = false
            ticketForm.reset()
        },
    })
}

const openTicket = (ticket) => {
    selectedTicket.value = ticket
    loadingDetail.value = true
    fetch(`/tickets/${ticket.id}/detail`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    })
        .then(r => r.json())
        .then(data => {
            ticketDetail.value = data
            loadingDetail.value = false
        })
}

const closeTicket = () => {
    selectedTicket.value = null
    ticketDetail.value = null
}

const changeStatus = (ticketId, action) => {
    router.post(`/tickets/${ticketId}/${action}`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            selectedTicket.value = null
            ticketDetail.value = null
        },
    })
}

const priorityColor = (priority) => {
    const colors = { low: 'text-green-600', medium: 'text-yellow-600', high: 'text-orange-600', critical: 'text-red-600' }
    return colors[priority] || 'text-gray-600'
}

const statusColor = (status) => {
    const colors = { open: 'bg-blue-100 text-blue-800', in_progress: 'bg-yellow-100 text-yellow-800', resolved: 'bg-green-100 text-green-800', closed: 'bg-gray-100 text-gray-800' }
    return colors[status] || 'bg-gray-100 text-gray-800'
}
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <div class="max-w-4xl mx-auto pt-8 px-4">
            <!-- Tab bar -->
            <div class="flex items-center bg-white rounded-t shadow-md">
                <button
                    @click="activeTab = 'welcome'"
                    :class="activeTab === 'welcome' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                    class="px-6 py-3 font-medium"
                >
                    Welcome
                </button>
                <button
                    @click="activeTab = 'profile'"
                    :class="activeTab === 'profile' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                    class="px-6 py-3 font-medium"
                >
                    Profile
                </button>
                <button
                    @click="activeTab = 'tickets'"
                    :class="activeTab === 'tickets' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                    class="px-6 py-3 font-medium"
                >
                    Tickets
                </button>
                <div class="ml-auto pr-4">
                    <button
                        @click="logout"
                        :disabled="logoutForm.processing"
                        class="bg-red-500 text-white px-4 py-1.5 rounded text-sm hover:bg-red-600 disabled:opacity-50"
                    >
                        Logout
                    </button>
                </div>
            </div>

            <!-- Tab content -->
            <div class="bg-white rounded-b shadow-md p-8">
                <!-- Welcome Tab -->
                <div v-if="activeTab === 'welcome'">
                    <h1 class="text-3xl font-bold mb-4">Welcome, {{ user.name }}</h1>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                        Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                    </p>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                        Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        Curabitur pretium tincidunt lacus. Nulla gravida orci a odio. Nullam varius, turpis et commodo pharetra,
                        est eros bibendum elit, nec luctus magna felis sollicitudin mauris. Integer in mauris eu nibh euismod gravida.
                    </p>
                </div>

                <!-- Profile Tab -->
                <div v-if="activeTab === 'profile'">
                    <h2 class="text-2xl font-bold mb-6">User Information</h2>
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div>
                            <span class="text-gray-500 text-sm">Name</span>
                            <p class="font-medium">{{ user.name }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 text-sm">Email</span>
                            <p class="font-medium">{{ user.email }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 text-sm">Member since</span>
                            <p class="font-medium">{{ new Date(user.created_at).toLocaleDateString() }}</p>
                        </div>
                    </div>

                    <h2 class="text-2xl font-bold mb-6">Profile Details</h2>
                    <div v-if="page.props.profile" class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-gray-500 text-sm">Phone</span>
                            <p class="font-medium">{{ page.props.profile.phone || '—' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 text-sm">Position</span>
                            <p class="font-medium">{{ page.props.profile.position || '—' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 text-sm">Department</span>
                            <p class="font-medium">{{ page.props.profile.department || '—' }}</p>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-500 text-sm">Bio</span>
                            <p class="font-medium">{{ page.props.profile.bio || '—' }}</p>
                        </div>
                    </div>
                    <p v-else class="text-gray-500">No profile information available.</p>
                </div>

                <!-- Tickets Tab -->
                <div v-if="activeTab === 'tickets'">
                    <!-- Create ticket form -->
                    <div v-if="showCreateForm && !isTechnician">
                        <button @click="showCreateForm = false" class="text-blue-500 hover:underline mb-4 text-sm">&larr; Back to tickets</button>
                        <h2 class="text-2xl font-bold mb-6">New Ticket</h2>
                        <form @submit.prevent="submitTicket">
                            <div class="mb-4">
                                <label class="block text-gray-700 mb-2" for="title">Title</label>
                                <input
                                    id="title"
                                    v-model="ticketForm.title"
                                    type="text"
                                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                />
                                <div v-if="ticketForm.errors.title" class="text-red-500 text-sm mt-1">{{ ticketForm.errors.title }}</div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 mb-2" for="project_id">Project</label>
                                <select
                                    id="project_id"
                                    v-model="ticketForm.project_id"
                                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                >
                                    <option value="" disabled>Select a project</option>
                                    <option v-if="!page.props.projects || !page.props.projects.length" disabled>No projects available</option>
                                    <option v-for="project in page.props.projects" :key="project.id" :value="project.id">
                                        {{ project.name }}
                                    </option>
                                </select>
                                <div v-if="ticketForm.errors.project_id" class="text-red-500 text-sm mt-1">{{ ticketForm.errors.project_id }}</div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 mb-2" for="description">Description</label>
                                <textarea
                                    id="description"
                                    v-model="ticketForm.description"
                                    rows="4"
                                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                ></textarea>
                                <div v-if="ticketForm.errors.description" class="text-red-500 text-sm mt-1">{{ ticketForm.errors.description }}</div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 mb-2" for="priority">Priority</label>
                                <select
                                    id="priority"
                                    v-model="ticketForm.priority"
                                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>

                            <div class="mb-6">
                                <label class="block text-gray-700 mb-2" for="details_file">Technical Details (JSON file)</label>
                                <input
                                    id="details_file"
                                    type="file"
                                    accept=".json"
                                    @change="onFileChange"
                                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                                <p class="text-gray-400 text-xs mt-1">Optional. Upload a JSON file with technical details.</p>
                                <div v-if="ticketForm.errors.details_file" class="text-red-500 text-sm mt-1">{{ ticketForm.errors.details_file }}</div>
                            </div>

                            <button
                                type="submit"
                                :disabled="ticketForm.processing"
                                class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 disabled:opacity-50"
                            >
                                Create Ticket
                            </button>
                        </form>
                    </div>

                    <!-- Ticket list -->
                    <div v-else-if="!selectedTicket">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold">{{ isTechnician ? 'All Tickets' : 'Your Tickets' }}</h2>
                            <button
                                v-if="!isTechnician"
                                @click="showCreateForm = true"
                                class="bg-green-500 text-white px-4 py-1.5 rounded text-sm hover:bg-green-600"
                            >
                                + New Ticket
                            </button>
                        </div>
                        <div v-if="page.props.tickets && page.props.tickets.length">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="border-b text-gray-500 text-sm">
                                        <th class="pb-2 pr-4">#</th>
                                        <th class="pb-2 pr-4">Title</th>
                                        <th v-if="isTechnician" class="pb-2 pr-4">Created by</th>
                                        <th class="pb-2 pr-4">Status</th>
                                        <th class="pb-2 pr-4">Priority</th>
                                        <th class="pb-2">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="ticket in page.props.tickets"
                                        :key="ticket.id"
                                        @click="openTicket(ticket)"
                                        class="border-b hover:bg-gray-50 cursor-pointer"
                                    >
                                        <td class="py-3 pr-4 text-gray-500">{{ ticket.id }}</td>
                                        <td class="py-3 pr-4 font-medium">{{ ticket.title }}</td>
                                        <td v-if="isTechnician" class="py-3 pr-4 text-gray-600">{{ ticket.user?.name || '—' }}</td>
                                        <td class="py-3 pr-4">
                                            <span :class="statusColor(ticket.status)" class="px-2 py-0.5 rounded text-xs font-medium">
                                                {{ ticket.status.replace('_', ' ') }}
                                            </span>
                                        </td>
                                        <td class="py-3 pr-4">
                                            <span :class="priorityColor(ticket.priority)" class="font-medium text-sm capitalize">
                                                {{ ticket.priority }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-gray-500 text-sm">{{ new Date(ticket.created_at).toLocaleDateString() }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p v-else class="text-gray-500">No tickets found.</p>
                    </div>

                    <!-- Ticket detail view -->
                    <div v-else>
                        <button @click="closeTicket" class="text-blue-500 hover:underline mb-4 text-sm">&larr; Back to tickets</button>
                        <h2 class="text-2xl font-bold mb-2">{{ selectedTicket.title }}</h2>
                        <div class="flex gap-3 mb-6">
                            <span :class="statusColor(selectedTicket.status)" class="px-2 py-0.5 rounded text-xs font-medium">
                                {{ selectedTicket.status.replace('_', ' ') }}
                            </span>
                            <span :class="priorityColor(selectedTicket.priority)" class="font-medium text-sm capitalize">
                                {{ selectedTicket.priority }}
                            </span>
                        </div>

                        <!-- Status action buttons (technician only) -->
                        <div v-if="isTechnician && (selectedTicket.status === 'open' || selectedTicket.status === 'in_progress')" class="flex gap-3 mb-6">
                            <button
                                v-if="selectedTicket.status === 'open'"
                                @click="changeStatus(selectedTicket.id, 'take')"
                                class="bg-yellow-500 text-white px-4 py-1.5 rounded text-sm hover:bg-yellow-600"
                            >
                                Take Ticket
                            </button>
                            <button
                                v-if="selectedTicket.status === 'open' || selectedTicket.status === 'in_progress'"
                                @click="changeStatus(selectedTicket.id, 'resolve')"
                                class="bg-green-500 text-white px-4 py-1.5 rounded text-sm hover:bg-green-600"
                            >
                                Resolve Ticket
                            </button>
                            <button
                                v-if="selectedTicket.status === 'open' || selectedTicket.status === 'in_progress'"
                                @click="changeStatus(selectedTicket.id, 'close')"
                                class="bg-gray-500 text-white px-4 py-1.5 rounded text-sm hover:bg-gray-600"
                            >
                                Close Ticket
                            </button>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-gray-500 text-sm mb-1">Description</h3>
                            <p class="text-gray-700">{{ selectedTicket.description || '—' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <span class="text-gray-500 text-sm">Project</span>
                                <p class="font-medium">{{ selectedTicket.project?.name || '—' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm">Created</span>
                                <p class="font-medium">{{ new Date(selectedTicket.created_at).toLocaleDateString() }}</p>
                            </div>
                        </div>

                        <!-- Ticket Details (1:1) -->
                        <div v-if="loadingDetail" class="text-gray-500">Loading details...</div>
                        <div v-else-if="ticketDetail">
                            <h3 class="text-xl font-semibold mb-4 border-t pt-4">Technical Details</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-gray-500 text-sm">Environment</span>
                                    <p class="font-medium">{{ ticketDetail.environment || '—' }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-sm">Browser</span>
                                    <p class="font-medium">{{ ticketDetail.browser || '—' }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-sm">Operating System</span>
                                    <p class="font-medium">{{ ticketDetail.operating_system || '—' }}</p>
                                </div>
                                <div class="col-span-2">
                                    <span class="text-gray-500 text-sm">Technical Notes</span>
                                    <p class="font-medium">{{ ticketDetail.technical_notes || '—' }}</p>
                                </div>
                                <div class="col-span-2">
                                    <span class="text-gray-500 text-sm">Steps to Reproduce</span>
                                    <p class="font-medium whitespace-pre-line">{{ ticketDetail.steps_to_reproduce || '—' }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-sm">Expected Behavior</span>
                                    <p class="font-medium">{{ ticketDetail.expected_behavior || '—' }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-sm">Actual Behavior</span>
                                    <p class="font-medium">{{ ticketDetail.actual_behavior || '—' }}</p>
                                </div>
                                <div class="col-span-2">
                                    <span class="text-gray-500 text-sm">Resolution</span>
                                    <p class="font-medium">{{ ticketDetail.resolution || '—' }}</p>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-gray-500 border-t pt-4">No technical details available.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>