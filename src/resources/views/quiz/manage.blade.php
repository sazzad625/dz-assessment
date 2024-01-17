@extends('layouts.app')
@section('title', 'Manage Quiz')
@push('page_css')
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
@endpush
@section('content')
    <style>
        .employee-form {
            background: #f8f8f8;
            padding: 20px;
        }
    </style>
    <div class="container">
        <div id="addQuizApp" v-cloak>
            <h1 class="text-black-40 main-title">@{{title}}</h1>
            <form id="form" method="POST" @submit.prevent="submit">
                <div class="container white-bg">
                    <div class="row">
                        <div class="form-group col-12 col-sm-6 col-md-6">
                            <label>Quiz Name *</label>
                            <input type="text" class="form-control"
                                   id="name"
                                   name="name"
                                   maxlength="125"
                                   v-model="name"
                                   required
                            />
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-6">
                            <label>Quiz Duration</label>
                            <input type="text" class="form-control"
                                   id="quizTimings"
                                   :value="dateRange"
                                   autocapitalize="off"
                            />
                        </div>

                        <div class="form-group col-12 col-sm-12 col-md-12">
                            <label>Quiz Description *</label>
                            <textarea class="form-control" placeholder="Quiz Description" maxlength="255"
                                      v-model="description"
                                      required></textarea>

                        </div>
                        <div class="form-group col-1 col-sm-1">
                            <label>Time Limit</label>
                            <label class="c-switch c-switch-label c-switch-pill c-switch-primary">
                                <input class="c-switch-input" type="checkbox" id="activeTimeLimit" v-model="isTimeLimit"
                                       name="activeTimeLimit">
                                <span class="c-switch-slider" data-checked="YES" data-unchecked="NO"></span>
                            </label>
                        </div>
                        <div class="form-group col-5 col-sm-5 col-md-5">
                            <label>Quiz Time Limit</label>
                            <div class="input-group">
                                <input type="number" class="form-control"
                                       min="1"
                                       max="120"
                                       required
                                       v-model="timeLimit"
                                       :disabled="!isTimeLimit"
                                />
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                      minutes
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-6">
                            <label>Passing Criteria *</label>
                            <div class="input-group">
                                <input type="number" class="form-control"
                                       min="1"
                                       max="100"
                                       required
                                       v-model="passingPercentage"
                                       :disabled="isQuizAttempted"
                                />
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                      %
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-6">
                            <label>Quiz Review</label>
                            <select class="form-control" v-model="allowReview">
                                <option value="true">Yes</option>
                                <option value="false">No</option>
                            </select>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-6">
                            <label>Number of Attempts Allowed</label>
                            <select class="form-control" v-model="attemptsAllowed">
                                <option v-for="n in 10" :disabled="isQuizAttempted && n < lastAttemptsAllowed">
                                    @{{n}}
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-6">
                            <label>Grading Option</label>
                            <select class="form-control" v-model="gradingTypeId" :disabled="isQuizAttempted">
                                <option v-for="(gradingOption, key) in gradingOptions" :value="key">@{{gradingOption}}
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-6">
                            <label>Type Of Quiz</label>
                            <select class="form-control" v-model="type" :disabled="isQuizAttempted">
                                <option v-for="(tempType) in types" :value="tempType">@{{tempType}}</option>
                            </select>
                        </div>
                        <div class="form-group col-12 col-sm-6 col-md-6">
                            <label>Max Questions</label>
                            <input type="number" v-model="maxQuestions" class="form-control" min="0"/>
                        </div>
                        <div class="form-group col-12 col-sm-6 col-md-6">
                            <label>Active</label>
                            <label class="c-switch c-switch-label c-switch-pill c-switch-primary">
                                <input class="c-switch-input" type="checkbox" id="active" v-model="isActive"
                                       name="active">
                                <span class="c-switch-slider" data-checked="YES" data-unchecked="NO"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row">
                        <div class="form-group col-md-2 col-sm-12">
                            <label>&nbsp;</label>
                            <button type="submit" id="btnSubmit" class="btn btn-primary btn-block" :disabled="isLoading">
                                @{{titleSubmit}}
                                <div v-if="isLoading" class="spinner-border spinner-border-sm" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </button>
                        </div>
                    </div>
            </form>
        </div>
        <br>
        <div id="quizQuestionsApp" v-cloak>
            <h2 class="text-black-40 main-title" id="addQuestions">Add Quiz Questions</h2>
            <div class="container white-bg">
                <div class="row">
                    <div class="col-12">
                        <div class="card mb-3" v-for="(question, index) in questions" :key="question">
                            <div class="card-body">
                                <span class="float-right" @click="confirmRemoveQuestion(index)"
                                      style="cursor: pointer;"
                                      v-if="!question.isDeleting"
                                >X</span>
                                <div v-else class="spinner-border spinner-border-sm float-right" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <h4 class="card-title">Question @{{ index + 1}}</h4>
                                <multi-choice v-if="question.type == 'Multi Choice'"
                                              :init-id="question.id"
                                              :init-name="question.name"
                                              :init-text="question.json.text"
                                              :init-options="question.json.options"
                                              :index="index"
                                              @on-question-update="onQuestionUpdate"
                                >
                                </multi-choice>
                            </div>
                        </div>

                        <div class="modal fade" id="deleteQuestion" tabindex="-1" role="dialog"
                             ref="deleteQuestionModal"
                             aria-labelledby="exampleModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Confirm</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form>
                                            <input type="hidden" id="modalDeleteCourseId"/>
                                            <p>Are you sure you want to delete this question?</p>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close
                                        </button>
                                        <button type="button" class="btn btn-primary btn-danger"
                                                @click="removeQuestion">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-success mt-3 mb-3" @click="addNewQuestion">
                            Add Question
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('page_scripts')

    <script src="https://cdn.ckeditor.com/4.16.1/standard/ckeditor.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="//unpkg.com/vue@3.0.11"></script>
    <script>
        (function () {
            let types =  @json(\App\Models\Quiz::TYPES);
            let gradingOptions = @json(\App\Models\QuizGradingType::getAllForOptions());
            let app = Vue.createApp({
                data: function () {
                    return {
                        id: {{!empty($quizId) ? $quizId : -1 }},
                        name: '',
                        startDate: null,
                        endDate: null,
                        description: '',
                        timeLimit: 0,
                        passingPercentage: 1,
                        attemptsAllowed: 1,
                        lastAttemptsAllowed: null,
                        allowReview: false,
                        maxQuestions: 1,
                        isActive: false,
                        types: types,
                        type: types[0],
                        gradingOptions: gradingOptions,
                        gradingTypeId: Object.keys(gradingOptions)[0],
                        isLoading: true,
                        isTimeLimit: false,
                        isQuizAttempted: false
                    }
                },
                computed: {
                    title: function () {
                        return (this.id === -1 ? 'Add' : 'Update') + " Quiz";
                    },
                    titleSubmit: function () {
                        return this.id === -1 ? 'Next' : 'Update';
                    },
                    serverUrl: function () {
                        if (this.id === -1) {
                            return "/course/{{$courseId}}/quiz/add";
                        } else {
                            return "/course/quiz/update/" + (this.id);
                        }
                    },
                    dateRange: function () {
                        if (this.startDate && this.endDate) {
                            return this.startDate + ' - ' + this.endDate;
                        }
                        return '';
                    }
                },
                watch: {
                    timeLimit: function (newValue) {
                        if (newValue < 0) {
                            this.timeLimit = 0;
                        }
                    },
                    passingPercentage: function (newValue) {
                        if (newValue < 0) {
                            this.passingPercentage = 1;
                        } else if (newValue > 100) {
                            this.passingPercentage = 100;
                        }
                    },
                    maxQuestions: function (newValue) {
                        if (newValue < 1) {
                            this.maxQuestions = 1;
                        }
                    },
                    isTimeLimit: function (value) {
                        if (!value) {
                            this.timeLimit = 0;
                        }
                    },
                },
                methods: {
                    submit: async function () {
                        this.isLoading = true;
                        let request = {
                            method: 'post',
                            headers: {
                                "Content-Type": "application/json; charset=utf-8",
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({
                                "_token": "{{csrf_token()}}",
                                name: this.name,
                                description: this.description,
                                startDate: this.startDate,
                                endDate: this.endDate,
                                timeLimit: this.timeLimit,
                                passingPercentage: this.passingPercentage,
                                attemptsAllowed: this.attemptsAllowed,
                                type: this.type,
                                allowReview: this.allowReview === 'true',
                                maxQuestions: this.maxQuestions,
                                isActive: this.isActive,
                                gradingTypeId: this.gradingTypeId
                            })
                        };

                        let response;
                        try {
                            response = await fetch(this.serverUrl, request);
                        } catch (e) {
                            alertModal('Error', "Unable to reach server");
                            return;
                        } finally {
                            this.isLoading = false;
                        }

                        let responseJson = await response.json();

                        if (response.ok) {
                            if (this.id === -1) {
                                quizQuestionsApp(responseJson.id);
                            }
                            if (responseJson.id) {
                                this.id = responseJson.id;
                            }
                            alertModal('success', 'Quiz saved.');
                        }
                    },
                    loadQuiz: async function () {

                        if (this.id === -1) {
                            this.isLoading = false;
                            return;
                        }

                        let serverUrl = "/quiz/fetch/" + this.id;
                        let request = {
                            method: "post",
                            headers: {
                                "Content-Type": "application/json; charset=utf-8",
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({
                                "_token": "{{csrf_token()}}"
                            })
                        };

                        let response = null;
                        try {
                            response = await fetch(serverUrl, request);
                        } catch (e) {
                            alertModal('Error', "Unable to reach server");
                            return;
                        }

                        let responseJson = await response.json();

                        if (!response.ok) {
                            alertModal("Error", responseJson.message);
                            return;
                        }

                        this.name = responseJson.name;
                        this.startDate = responseJson.startDate;
                        this.endDate = responseJson.endDate;
                        this.description = responseJson.description;
                        this.timeLimit = responseJson.timeLimit;
                        this.passingPercentage = responseJson.passingPercentage;
                        this.allowReview = responseJson.allowReview;
                        this.attemptsAllowed = responseJson.attemptsAllowed;
                        this.lastAttemptsAllowed = responseJson.attemptsAllowed;
                        this.maxQuestions = responseJson.maxQuestions;
                        this.isActive = responseJson.isActive;
                        this.type = responseJson.type;
                        this.gradingTypeId = responseJson.gradingTypeId;
                        if (this.timeLimit) {
                            this.isTimeLimit = true;
                        }
                        this.isLoading = false;
                        this.isQuizAttempted = responseJson.isQuizAttempted;
                    }
                },
                mounted: function () {
                    let component = this;
                    $('#quizTimings').daterangepicker({
                        autoUpdateInput: false,
                        showDropdowns: true
                    }, function (start, end) {
                        component.startDate = start.format('YYYY-MM-DD');
                        component.endDate = end.format('YYYY-MM-DD');
                    });

                    this.loadQuiz();
                }
            });
            app.mount('#addQuizApp');
        })();
    </script>
    <script src="{{asset('/js/vue/quiz-question-app.js')}}"></script>
    <script>
        @if(!empty($quizId))
        quizQuestionsApp({{$quizId}});
        @endif
    </script>
@endpush
