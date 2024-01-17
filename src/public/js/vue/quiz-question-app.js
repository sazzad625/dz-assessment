function quizQuestionsApp(quizId) {
    let csrf = document.getElementsByName('csrf-token')[0].getAttribute('content');
    let app = Vue.createApp({
        data: function () {
            return {
                quizId: quizId,
                questions: [],
                type: 'Multi Choice',
                selectedIndex: 0,
            };
        },
        computed: {
            questionsCount: function () {
                return this.questions.length;
            }
        },
        watch: {
            questionsCount: function (lenght) {
                if (lenght === 0) {
                    this.addEmptyQuestion();
                }
            }
        },
        methods: {
            addEmptyQuestion: function () {
                this.questions.push({
                    id: -1,
                    type: this.type,
                    name: '',
                    json: {
                        text: '',
                        options: [
                            {text: '', isCorrect: false}
                        ]
                    }
                });
            },
            addNewQuestion: function () {
                this.addEmptyQuestion()
            },
            confirmRemoveQuestion: function (index) {
                if (this.questions[index].id === -1) {
                    this.questions.splice(index, 1);
                    return;
                }

                this.selectedIndex = index;
                $(this.$refs.deleteQuestionModal).modal('show');
            },
            removeQuestion: async function () {
                $(this.$refs.deleteQuestionModal).modal('hide');
                let index = this.selectedIndex;
                this.questions[index].isDeleting = true;

                let questionId = this.questions[index].id;

                let serverUrl = "/quiz/question/" + (questionId) + "/delete";
                let request = {
                    method: "post",
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        _token: csrf
                    })
                };

                let response;

                try {
                    response = await fetch(serverUrl, request);
                } catch (e) {
                    alertModal("Error", "Unable to reach server");
                    return;
                } finally {
                    this.questions[index].isDeleting = false;
                }

                if (response.ok) {
                    this.questions.splice(index, 1);
                }
            },
            load: async function () {
                let serverUrl = "/quiz/" + (this.quizId) + "/questions/fetch";
                let request = {
                    method: "post",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        "_token": csrf
                    })
                };

                let response = null;

                try {
                    response = await fetch(serverUrl, request);
                } catch (e) {
                    alertModal('Error', 'Unable to reach server');
                    return;
                }

                if (!response.ok) {
                    alertModal('Error', 'Unexpected response from server');
                    return;
                }

                let responseJson = await response.json();

                let component = this;
                responseJson.questions.forEach(function (question) {
                    component.questions.push({
                        id: question.id,
                        type: question.type,
                        name: question.name,
                        json: question.json
                    });
                });

                if (responseJson.questions.length === 0) {
                    this.addEmptyQuestion();
                }
            },
            onQuestionUpdate: function (index, payload) {
                this.questions[index].id = payload.id;
                this.questions[index].name = payload.name;
                this.questions[index].json = payload.json;
            }
        },
        mounted: function () {
            this.load();
            window.location = '#addQuestions';
        }
    });

    //component for multi-choice
    app.component('multi-choice', {
        props: ["initId", "initName", "initText", "initOptions", "index"],
        data: function () {
            return {
                id: this.initId ? this.initId : -1,
                name: this.initName ? this.initName : '',
                text: this.initText ? this.initText : '',
                options: this.initOptions ? JSON.parse(JSON.stringify(this.initOptions)) : [
                    {text: '', isCorrect: false}
                ],
                isLoading: false,
                type: 'Multi Choice'
            };
        },
        watch: {
            id: function () {
                this.updateParentQuestion();
            },
            name: function () {
                this.updateParentQuestion();
            },
            text: function () {
                this.updateParentQuestion();
            },
            options: {
                handler: function () {
                    this.updateParentQuestion();
                },
                deep: true
            }
        },
        methods: {
            addOption: function () {
                this.options.push({
                    text: '', isCorrect: false
                });
            },
            removeOption: function (option) {
                let index = this.options.indexOf(option)
                this.options.splice(index, 1);
            },
            submit: async function () {
                this.isLoading = true;
                if (!this.validate()) {
                    this.isLoading = false;
                    return;
                }
                let request = {
                    method: "post",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        "_token": csrf,
                        type: this.type,
                        name: this.name,
                        json: JSON.stringify({
                            text: this.text,
                            options: this.options
                        })
                    })
                };

                let response;
                try {
                    response = await fetch(this.serverUrl, request);
                } catch (e) {
                    alertModal('Error', 'Unable to reach server');
                    return;
                } finally {
                    this.isLoading = false;
                }

                let responseJson = await response.json();

                if (!response.ok) {
                    alertModal('Error', responseJson.message);
                    return;
                }

                let messageCreate = "Question created";
                let messageUpdate = "Question updated";

                alertModal('Success', this.id === -1 ? messageCreate : messageUpdate);

                this.id = responseJson.id;
            },
            updateParentQuestion() {
                this.$emit('onQuestionUpdate', this.index, {
                    id: this.id,
                    type: this.type,
                    name: this.name,
                    json: {
                        text: this.text,
                        options: this.options
                    }
                });
            },
            validate: function () {
                let errorMessage = "";
                let errorCount = 0;

                if (this.name.trim().length === 0) {
                    errorCount++;
                    errorMessage += "- Name can not be empty<br/>";
                }

                if (this.text.trim().length === 0) {
                    errorCount++;
                    errorMessage += "- Question can not be empty<br/>";
                }

                let checkedCount = 0;
                this.options.forEach(function (option, index) {
                    if (option.text.trim().length === 0) {
                        errorCount++;
                        errorMessage += "- Option " + (index + 1) + " can not be empty<br/>";
                    }
                    if (option.isCorrect) {
                        checkedCount++;
                    }
                });

                if (checkedCount === 0) {
                    errorCount++;
                    errorMessage += "- One option must be marked as correct<br/>";
                }

                if (errorCount !== 0) {
                    alertModal("Validation failed for question " + (this.index + 1), errorMessage);
                    return false;
                }

                return true;
            }
        },
        computed: {
            submitButtonTitle: function () {
                return this.id === -1 ? "Save" : "Update";
            }
            ,
            serverUrl: function () {
                return this.id === -1 ?
                    '/quiz/' + quizId + '/question/add' :
                    '/quiz/question/' + (this.id) + '/update';
            }
        }
        ,
        mounted: function () {
            let instance = CKEDITOR.replace(this.$refs.editor, {
                UploadUrl: '/file-manager/ckeditor',
                filebrowserUploadUrl: '/file-manager/ckeditor',
                filebrowserBrowseUrl: '/file-manager/ckeditor',
            });
            let component = this;
            instance.on('change', function () {
                component.text = this.getData();
            });
        }
        ,
        template: `
                    <div>
                        <label>Name *</label>
                        <input type="text" class="form-control" v-model="name" placeholder="For searching in question bank" /><br/>
                        <textarea class="form-control" placeholder="Question" v-model="text" ref="editor"></textarea> <br>
                        <div class="employee-form" v-for="(option, index) in options">
                            <div class="row">
                                <div class="col-8">
                                    <input type="text" class="form-control" placeholder="Option"
                                           v-model="option.text"/>
                                </div>
                                <div class="col-2">
                                    <label><input type="checkbox" v-model="option.isCorrect"/> Correct</label>
                                </div>
                                <div class="col-2">
                                    <button type="button" @click="removeOption(option)" class="btn btn-danger" :disabled="options.length < 2"> Remove</button>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-success mt-3 mb-2" @click=addOption>Add Option</button>
                        <button class="btn btn-primary mt-3 mb-2 float-right" @click=submit :disabled="isLoading">
                            {{submitButtonTitle}}
                            <div v-if="isLoading" class="spinner-border spinner-border-sm" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </button>
                    </div>
                `
    });

    //mounting app
    app.mount("#quizQuestionsApp");
};
