@extends('layouts.app')
@section('title', 'Manage Course Content')
@section('content')
    <div class="container">
                <h1 class="text-black-40 main-title">Manage Course Content</h1>

        <div v-cloak id="editorApp">
            <div class="row">
                <div class="col-12">
                    <textarea id="editor"></textarea>
                </div>
            </div>
            <br/>
            <div class="row">
                <div class="form-group col-md-2 col-sm-12">
                    <label>&nbsp;</label>
                    <button type="button" id="btnSubmit" class="btn btn-primary" @click="submit" :disabled="isLoading">
                        Submit
                        <div v-if="isLoading" class="spinner-border spinner-border-sm" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div v-cloak id="attachmentsApp">
            <h1 class="text-black-40 main-title">Attachments (@{{attachmentsCount}})</h1>
            <div class="form-row" v-for="(attachment, index) in attachments" :key="index">
                <div class="form-group col-md-6">
                    <label for="image_label">Attachment @{{index + 1}}</label>
                    <div class="input-group">
                        <input type="text" class="form-control" :id="'input_attachment_' + index"
                               aria-label="Image" aria-describedby="button-image" :value="attachment.uri" readonly/>
                        <div class="input-group-append">
                            <button v-if="attachment.id == -1" class="btn btn-outline-secondary" type="button"
                                    @click="browse(index)">Browse
                            </button>
                        </div>
                        &nbsp; &nbsp;
                        <button class="btn btn-primary btn-danger" @click="confirmRemove(index)"
                                :disabled="attachment.isLoading">
                            <div v-if="attachment.isLoading" class="spinner-border spinner-border-sm" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <template v-else>Remove</template>
                        </button>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary" @click="addNew" :disabled="isLoading">Add New</button>
            <br>
            <br>

            <div class="modal fade" id="deleteAttachment" tabindex="-1" role="dialog"
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
                                <p>Are you sure you want to delete this attachment?</p>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary btn-danger" @click="remove">Remove
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script src="{{asset('ckeditor/ckeditor.js')}}"></script>
    <script src="//unpkg.com/vue@3.0.11"></script>
    <script>
        (function () {
            let app = Vue.createApp({
                data: function () {
                    return {
                        isLoading: true
                    }
                },
                mounted: function () {
                    // this.isLoading = false;
                    let component = this;
                    CKEDITOR.replace('editor', {
                        UploadUrl: '/file-manager/ckeditor',
                        filebrowserUploadUrl: '/file-manager/ckeditor',
                        filebrowserBrowseUrl: '/file-manager/ckeditor',
                        height: '600px'
                    });

                    fetch("{{route('course.content.get', [$courseId, $contentId])}}", {
                        method: 'post',
                        headers: {
                            "Content-Type": "application/json; charset=utf-8",
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({
                            "_token": "{{csrf_token()}}"
                        })
                    }).then(function (response) {
                        if (!response.ok) {
                            throw new Error("Unable to fetch content");
                        }
                        return response.json();
                    }).then(function (response) {
                        CKEDITOR.instances.editor.setData(response.contentBody);
                        component.isLoading = false;
                    }).catch(function () {
                        alertModal("Error", "Unable to fetch content");
                    })
                },
                methods: {
                    submit() {
                        this.isLoading = true;
                        let component = this;
                        let data = CKEDITOR.instances.editor.getData();
                        fetch("{{route('course.content.save', [$courseId, $contentId])}}", {
                            method: 'post',
                            headers: {
                                "Content-Type": "application/json; charset=utf-8",
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({
                                "_token": "{{csrf_token()}}",
                                contentBody: data
                            })
                        }).then(function (response) {
                            if (!response.ok) {
                                throw new Error("Unable to save");
                            }
                            alertModal("Success", "Content saved.");
                        }).catch(function () {
                            alertModal("Error", "Unable to save");
                            console.error('error occurred');
                        }).finally(function () {
                            component.isLoading = false;
                        });
                    }
                }
            });
            app.mount('#editorApp');
        })();
    </script>
    <script>

        let selectedElementIndex = null;

        let app = Vue.createApp({
            data: function () {
                return {
                    isLoading: true,
                    attachments: [],
                    removeIndex: -1,
                    test: 123
                }
            },
            computed: {
                attachmentsCount: function () {
                    return this.attachments.length;
                }
            },
            watch: {
                attachmentsCount: function (newValue, oldValue) {
                    if (newValue == 0) {
                        this.attachments.push({
                            id: -1,
                            uri: '',
                            isLoading: false
                        });
                    }
                }
            },
            methods: {
                addNew: function () {
                    this.attachments.push({
                        id: -1,
                        isLoading: false
                    });
                },
                browse: function (index) {
                    selectedElementIndex = index;
                    window.open('/file-manager/fm-button', 'fm', 'width=1400,height=800')
                },
                uriChanged: function (index, uri) {
                    let component = this;
                    component.attachments[index].isLoading = true
                    component.attachments[index].uri = uri;
                    fetch('{{route('course.content.attachment.add', [$courseId, $contentId])}}', {
                        method: 'post',
                        headers: {
                            "Content-Type": "application/json; charset=utf-8",
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({
                            "_token": "{{csrf_token()}}",
                            "uri": component.attachments[index].uri
                        })
                    }).then(function (response) {
                        if (!response.ok) {
                            throw new Error("unable to save");
                        }
                        return response.json();

                    }).then(function (response) {
                        component.attachments[index].id = response.id;
                        alertModal('Success', 'Attachment saved ' + component.attachments[index].uri.split('/').pop());
                    }).catch(function () {
                        alertModal('Error', 'Unable to save attachment ' + component.attachments[index].uri.split('/').pop());
                        component.attachments.splice(index, 1);
                    }).finally(function () {
                        component.attachments[index].isLoading = false;
                    })
                },
                confirmRemove: function (index) {
                    this.removeIndex = index;
                    if (this.attachments[index].id == -1) {
                        this.remove();
                    } else {
                        $('#deleteAttachment').modal('show');
                    }

                },
                remove: function () {
                    $('#deleteAttachment').modal('hide');
                    let component = this;
                    let index = this.removeIndex;
                    component.attachments[index].isLoading = true;

                    //if attachment is not stored in database yet then only remove it from ui
                    if (component.attachments[index].id == -1) {
                        component.attachments.splice(index, 1);
                        return;
                    }

                    let serverUrl = "{{route('course.content.attachment.delete', [$courseId, $contentId])}}";
                    fetch(serverUrl, {
                        method: 'post',
                        headers: {
                            "Content-Type": "application/json; charset=utf-8",
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({
                            "_token": "{{csrf_token()}}",
                            "id": component.attachments[index].id
                        })
                    }).then(function (response) {
                        if (!response.ok) {
                            throw new Error("Unable to delete");
                        }
                        component.attachments.splice(index, 1);

                    }).catch(function (error) {
                        alertModal('Error', 'Unable to remove');
                        component.attachments[index].isLoading = false;
                    }).finally(function () {
                        alertModal('Success', 'Attachment removed');
                    })
                }
            },
            mounted: function () {
                let component = this;
                fetch('{{route('course.content.attachments.get', [$courseId, $contentId])}}', {
                    method: 'post',
                    headers: {
                        "Content-Type": "application/json; charset=utf-8",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        "_token": "{{csrf_token()}}"
                    })
                }).then(function (response) {
                    if (!response.ok) {
                        throw new Error("Unable to fetch attachments");
                    }
                    component.isLoading = false;
                    return response.json();
                }).then(function (response) {
                    response.data.forEach(function (attachment) {
                        component.attachments.push({
                            id: attachment.id,
                            uri: attachment.uri,
                            isLoading: false
                        })
                    })

                    if (response.data.length < 1) {
                        component.attachments.push({
                            id: -1,
                            uri: '',
                            isLoading: false
                        });
                    }
                }).catch(function (error) {
                    alertModal('Error', "Unable to fetch attachments");
                }).finally(function () {
                    component.isLoading = false;
                })
            }
        });

        let attachmentsApp = app.mount('#attachmentsApp');

        function fmSetLink(url) {
            var storagePrefix = "{{\App\Helpers\PathHelper::getStoragePath()}}";
            var storagePath = "{{\App\Helpers\PathHelper::getStoragePath()}}";
            var uri = storagePath + url.substr(url.indexOf(storagePrefix) + storagePrefix.length);
            attachmentsApp.uriChanged(selectedElementIndex, uri);
        }
    </script>
@endpush
