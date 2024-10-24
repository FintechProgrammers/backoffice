import { createRouter, createWebHistory } from 'vue-router';

// Import your Vue components

// Define your routes
const routes = [
    {
        path: '/',
        name: 'dashboard',
        component: () => import('./views/Dashboard.vue'),
    },
    {
        path: '/signals',
        name: 'signals',
        component: () => import('./views/Signals.vue'),
    },
];

// Create the router instance
const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
