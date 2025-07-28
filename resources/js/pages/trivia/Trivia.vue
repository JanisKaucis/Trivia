<script setup lang="ts">
import { Button } from '@/components/ui/button';
import axios from 'axios';
import { ref } from 'vue';

const gameStatus = ref(0);
const question = ref('');
const answers = ref('');
const win = ref('');
const lose = ref('');
const errors = ref({});
const userAnswer = ref('');

function startTrivia() {
    gameStatus.value = 1;
    getQuestion();
}

function getQuestion() {
    axios.get(route('question')).then(function (response) {
        const data = response.data;
        question.value = data.question;
        answers.value = data.answers;
    });
}

const submitForm = async () => {
    errors.value = {};
    const answer = userAnswer.value;
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
                answers.value = data.answers;

                if (win.value || lose.value) {
                    gameStatus.value = 2;
                }
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
            <div v-if="gameStatus === 0">
                <Button @click="startTrivia">Start Trivia</Button>
            </div>
            <div v-if="gameStatus === 1">
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <div>
                            <p>{{ question }}</p>
                            <div>
                                <div class="mt-2" v-for="answer in answers" :key="answer">
                                    <input type="radio" :id="answer" :value="answer" v-model="userAnswer" />
                                    <label :for="answer">{{ answer }}</label>
                                </div>
                                <p v-if="errors.answer" class="text-sm text-red-600">{{ errors.answer[0] }}</p>
                                <div class="mt-2 flex items-center">
                                    <div class="mx-auto">
                                        <Button type="submit">Next</Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div v-if="gameStatus === 2">

                <p v-if="lose" class="text-sm text-red-600">{{ lose }}</p>
                <p v-if="win" class="text-sm text-green-600">{{ win }}</p>
                <div class="mt-2 flex items-center">
                    <div class="mx-auto">
                        <Button @click="startTrivia">Play Again</Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
