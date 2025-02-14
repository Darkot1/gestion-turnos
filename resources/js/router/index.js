import { createRouter, createWebHistory } from 'vue-router';
import Index from '@/Pages/Modules/Index.vue';
import GenerateTurn from '@/Pages/Turns/GenerateTurn.vue';
import Shifts from '@/Pages/Shifts/Index.vue';

const routes = [
    {
        path: '/modules',
        name: 'Modules',
        component: Index
    },
    {
        path: '/generate-turn',
        name: 'GenerateTurn',
        component: GenerateTurn
    },
    {
        path: '/shifts',
        name: 'Shifts',
        component: Shifts
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
