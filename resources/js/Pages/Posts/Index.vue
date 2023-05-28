<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton  from '@/Components/PrimaryButton.vue';
import Form from '@/Pages/Posts/Form.vue';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import EditIcon from '../../Components/EditIcon.vue';
import ChatsContainer from '../../Components/ChatsContainer.vue';

const posts = ref([]);
const paginateLinks = ref([]);
const formRef = ref();
const chatsContainerRef = ref();

const getPosts = () => {
    axios.get(route('posts.getData'))
    .then(response => {
        posts.value = response.data.data; 
        paginateLinks.value = response.data.links;
    });
}

const openForm = (post = null) => {
    formRef.value.open(post);
}

const openChat = post => {
    chatsContainerRef.value.newChat(post);
}

onMounted(() => {
    getPosts();
});

</script>

<template>
    <AppLayout title="Posts">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Posts
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="text-right mb-4">
                    <PrimaryButton @click="openForm()">
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
                    class="bg-white overflow-hidden shadow-xl mb-4 sm:rounded-lg"
                >
                    <div class="grid gap-0 grid-cols-[1fr_100px] border-b p-4">
                        <div>
                            <h2 class="text-xl">{{ post.title }}</h2>
                        </div>
                        <div class="text-right">
                            <button 
                                v-if="post.user_id == $page.props.auth.user.id"
                                class="hover:text-gray-100"
                                @click="openForm(post)"
                            >
                                <EditIcon />
                            </button>
                        </div>
                    </div>
                    <div class="grid gap-0 grid-cols-1 p-4 sm:grid-cols-[400px_1fr] sm:gap-4">
                        <img :src="post.image" :alt="post.title" height="200">
                        <div>
                            <div class="mb-4 text-justify">
                                {{ post.description }}
                            </div>
                            
                            <PrimaryButton 
                                class="px-8"
                                :disabled="$page.props.auth.user.id == post.user_id"
                                @click="openChat(post)"
                            >
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
        ref="formRef"
        @post:added="getPosts"
    />

    <ChatsContainer ref="chatsContainerRef"></ChatsContainer>
</template>
