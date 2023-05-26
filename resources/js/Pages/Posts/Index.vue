<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton  from '@/Components/PrimaryButton.vue';
import Form from '@/Pages/Posts/Form.vue';
import { onMounted, ref } from 'vue';
import axios from 'axios';

const showForm = ref(false);
const posts = ref([]);
const paginateLinks = ref([]);

const getPosts = () => {
    axios.get(route('posts.getData'))
    .then(response => {
        posts.value = response.data.data; 
        paginateLinks.value = response.data.links;
        console.log(paginateLinks.value);
    });
}

onMounted(() => {
    getPosts();
});

</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Posts
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="text-right mb-4">
                    <PrimaryButton @click="showForm = true">
                        New Post
                    </PrimaryButton>
                </div>

                <!-- No post message -->
                <div 
                    v-if="!posts.length"
                    class="bg-white overflow-hidden shadow-xl text-center sm:rounded-lg"
                >
                    <h2 class="text-lg p-8">
                        There are not any post yet
                    </h2>
                </div>

                <!-- Post list -->
                <div 
                    v-else
                    v-for="post in posts" 
                    class="bg-white overflow-hidden shadow-xl p-4 mb-4 sm:rounded-lg"
                >
                    <h1 class=" text-xl">
                        {{ post.title }}
                    </h1>
                    <div class="grid gap-0  grid-cols-[400px_1fr] sm:gap-4">
                        <img :src="post.image" :alt="post.title" height="200">
                        <div>
                            <div class="mb-4 text-justify">
                                {{ post.description }}s
                            </div>
                            
                            <PrimaryButton class="px-8">
                                Chat
                            </PrimaryButton>
                        </div>
                    </div>
                </div>

                <!-- Paginate links -->
                <ul 
                    v-if="posts.length"
                    class="inline-flex items-center -space-x-px"
                >
                    <li
                        v-for="(link, i) in paginateLinks"
                    >
                        <button
                            class="px-3 py-2 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-800"
                            :class="[{
                                'rounded-l-lg': i == 0, 
                                'rounded-r-lg': i == paginateLinks.length - 1, 
                                'text-grey-800 bg-grey-50 hover:bg-grey-100 hover:text-grey-900': link.active,
                            }]"
                            @click.prevent="getPage(link.url)"
                            :title="link.url"
                        >
                            <span v-html="link.label"></span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>

    <!-- Post form -->
    <Form
        :show="showForm"  
        @close="showForm = false" 
        @post:added="getPosts"
    />
</template>
