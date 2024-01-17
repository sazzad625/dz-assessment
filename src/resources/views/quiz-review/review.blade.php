@extends('layouts.app')

@section('title', 'Quizzes')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body" id="quizAttemptApp" v-cloak>
                        <h1 class="text-black-40 main-title">Quiz</h1>
                        <div class="row">
                            <div class="col col-8">
                                <div class="card" v-for="(question, index) in questions">
                                    <div class="card-body">
                                        <h2 class="quiz-question"><span>Question @{{ index+1 }}.</span></h2>
                                        <div v-html="question.text"></div>
                                        <div class="quiz-options">
                                            <multi-choice
                                                v-if="question.type == 'Multi Choice'"
                                                :index="index"
                                                :options="question.options"
                                                :answers="question.answers"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script src="//unpkg.com/vue@3.0.11"></script>
    <script>
        (function () {
            let app = Vue.createApp({
                data: function () {
                    return {
                        questions: @json($questions),
                    }
                },
                mounted: function(){
                    console.log(this.questions);
                }
            });

            //multi choice component
            app.component('multi-choice', {
                props: ['options', 'answers', 'index'],
                data: function () {
                    return {
                        //answers: this.initAnswers
                    };
                },
                template: `
                <div class="row">
                    <div class="form-check col col-6" v-for="(option, index) in options">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" disabled :value="index" :checked="answers.includes(index)"/>
                            <span><span></span>@{{ option.text }}
                                <template v-if="answers.includes(index)">
                                    <template v-if="option.isCorrect"><i class="fa fa-check text-success"></i></template>
                                    <template v-else><i class="fa fa-times text-danger"></i></template>
                                </template>
                            </span>
                        </label>
                    </div>
                </div>
                `
            });
            app.mount('#quizAttemptApp');
        })();
    </script>
@endpush
