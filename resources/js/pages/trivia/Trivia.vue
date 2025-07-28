<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import axios from 'axios';
import $ from 'jquery';
import { onMounted, ref } from 'vue';

const question = ref('');
const win = ref('');
const lose = ref('');
const errors = ref({});
const userAnswer = ref('');

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
                win.value = data.win;
               lose.value = data.lose;
            });
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
        }
    }
    userAnswer.value = '';
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
                            <Input v-model="userAnswer" id="answer" type="number" />
                            <Button type="submit">Next</Button>
                        </div>
                        <p v-if="errors.answer" class="text-sm text-red-600">{{ errors.answer[0] }}</p>
                        <p v-if="lose" class="text-sm text-red-600">{{ lose }}</p>
                        <p v-if="win" class="text-sm text-green-600">{{ win }}</p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped></style>
