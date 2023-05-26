<script setup>
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { computed, ref } from 'vue';
import PrimaryButton from '../../Components/PrimaryButton.vue';
import axios from 'axios';

const emit = defineEmits(['close', 'post:added']);

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    post: {
        type: Object,
        default: () => ({})
    }
});

const form = ref({
    name: '',
    description: '',
    image: ''
});

const errors = ref({});
const imageInputRef = ref();
const imagePreviewUrl = ref(null);

const editMode = computed(() => {
    return Object.keys(props.post).length;
});

const submitRoute = computed(() => {
    return editMode.value ? 'posts.update' : 'posts';
});

const closeModal = () => {
    form.value = {
        name: '',
        description: '',
        image: ''
    }

    imageInputRef.value = '';
    imagePreviewUrl.value = '';

    emit('close')
}

const previewImage = () => {
    const reader = new FileReader();
    const file = imageInputRef.value.files[0];

    reader.readAsDataURL(file);
    reader.onload = () => {
        if(file.type.split("/")[0] == 'image'){
            const img = new Image();
            img.src = reader.result;
            img.onload = () => {
                form.value.image = file;
                imagePreviewUrl.value = reader.result;
            };
        }
    };
}

const submit = () => {
    const formData = new FormData();

    formData.append('title', form.value.title)
    formData.append('description', form.value.description)
    formData.append('image', form.value.image);

    axios.post(route(submitRoute.value), formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
    })
    .then(response => {
        closeModal();

        emit('post:added');
    })
    .catch(error => {
        let errorCode = 0;

        if(error.response) {
            errorCode = error.response.status;
        }

        // Si es un error de validacion seteamos los errores
        if(errorCode == 422) {
            errors.value = error.response.data.errors;
        }   
    });
}

</script>

<template>
    <DialogModal :show="show" @close="closeModal" >
        <template #title>
            <span v-if="!editMode">
                New Post
            </span>
            <span v-else>
                Edit post: {{ post.title }}
            </span>
        </template>

        <template #content>
            <div class="mt-4">
                <InputLabel>
                    Image
                </InputLabel>

                <div v-if="imagePreviewUrl" class="m-4">
                    <img 
                        :src="imagePreviewUrl" 
                        alt="Post Image"
                        height="200"
                        width="200"
                    >
                </div>

                <input
                    ref="imageInputRef"
                    type="file"
                    class="mt-1 block w-full"
                    @change="previewImage"
                />

                <InputError :message="errors.image ? errors.image[0] : ''" class="mt-2" />
            </div>
            <div class="mt-4">
                <InputLabel>
                    Title
                </InputLabel>

                <TextInput
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="The title of post"
                />

                <InputError :message="errors.title ? errors.title[0] : ''" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel>
                    Description
                </InputLabel>

                <TextInput
                    v-model="form.description"
                    type="textarea"
                    class="mt-1 block w-full"
                    placeholder="The description of post"
                />

                <InputError :message="errors.description ? errors.description[0]: ''" class="mt-2" />
            </div>
        </template>

        <template #footer>
            <SecondaryButton class="mr-2" @click="closeModal">
                Cancel
            </SecondaryButton>

            <PrimaryButton @click="submit">
                <span v-if="editMode">
                    Update
                </span>
                <span v-else>
                    Save
                </span>
            </PrimaryButton>
        </template>
    </DialogModal>
</template>