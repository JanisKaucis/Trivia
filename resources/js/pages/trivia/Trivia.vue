<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import axios from 'axios';
import $ from 'jquery';
import { onMounted, ref } from 'vue';

const question = ref('');
const errors = ref({});
const form = ref({
    answer: '',
});

onMounted(() => {
    getQuestion();
});

function getQuestion() {
    axios.get(route('question')).then(function (response) {
        const data = response.data;
        question.value = data.question;
    });
}

const submitForm = async () => {
    errors.value = {};
    const answer = $('#answer').val();
    try {
        await axios
            .post(
                route('answer', {
                    answer: answer,
                }),
            )
            .then(function (response) {
                const data = response.data;
                if (response.status !== 200) {
                    errors.value = response.data.errors;
                }
                question.value = data.question;
            });
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
        }
    }
    form.value = {
        answer: '',
    };
};
</script>

<template>
    <div class="flex h-screen items-center">
        <div class="mx-auto">
            <form @submit.prevent="submitForm" class="space-y-4">
                <div>
                    <div>
                        <p>{{ question }}</p>
                        <div>
                            <Input v-model="form.answer" id="answer" type="number" />
                            <Button type="submit">Next</Button>
                        </div>
                        <p v-if="errors.answer" class="text-sm text-red-600">{{ errors.answer[0] }}</p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped></style>
