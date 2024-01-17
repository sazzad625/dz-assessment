(function () {
    let app = Vue.createApp({
        data: function () {
            return {
                courseCount: 0,
                categoryCount: 0,
                teacherCount: 0,
                attemptedQuizzesCount: 0,
                participantCount: [],
            };
        },
        mounted: async function () {
            let component = this;
            this.getData('/course-count').then(function (response) {
                component.courseCount = response.count;
            });
            this.getData('/category-count').then(function (response) {
                component.categoryCount = response.count;
            });
            this.getData('/teacher-count').then(function (response) {
                component.teacherCount = response.count;
            });
            this.getData('/attempted-quiz-count').then(function (response) {
                component.attemptedQuizzesCount = response.count;
            });
            this.getData('/student-count').then(function (response) {
                component.participantCount = response.data;
            });
        },
        methods: {
            getData: async function (serverUrl) {
                let response;
                try {
                    response = await fetch(serverUrl, {
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });
                } catch (e) {
                    return false;
                }

                if (!response.ok) {
                    return false;
                }
                let responseJson = await response.json();
                return responseJson;
            }
        }
    });
    app.mount('#CountApp');
})();
