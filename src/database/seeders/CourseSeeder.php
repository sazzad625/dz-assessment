<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::find(1);

        $user1 = new User();
        $user1->name = "Student 1";
        $user1->email = "student1@domain.com";
        $user1->password = bcrypt("secret");
        $user1->type = User::USER_TYPE_STUDENT;
        $user1->save();

        $user2 = new User();
        $user2->name = "Student 2";
        $user2->email = "student2@domain.com";
        $user2->password = bcrypt("secret");
        $user2->type = User::USER_TYPE_STUDENT;
        $user2->save();

        $user3 = new User();
        $user3->name = "Student 3";
        $user3->email = "student3@domain.com";
        $user3->password = bcrypt("secret");
        $user3->type = User::USER_TYPE_STUDENT;
        $user3->save();

        $user4 = new User();
        $user4->name = "Student 4";
        $user4->email = "student4@domain.com";
        $user4->password = bcrypt("secret");
        $user4->type = User::USER_TYPE_STUDENT;
        $user4->save();

        $category1 = new CourseCategory();
        $category1->name = "Category 1";
        $category1->description = "Test description 1";
        $category1->image_path = "public/2021/April/";
        $category1->image_name = "06-46-30-608661c67787a-AdminLTELogo.png";
        $category1->is_active = true;
        $category1->fk_created_by = 1;
        $category1->save();

        $category2 = new CourseCategory();
        $category2->name = "Category 2";
        $category2->description = "Test description 2";
        $category2->image_path = "public/2021/April/";
        $category2->image_name = "06-46-30-608661c67787a-AdminLTELogo.png";
        $category2->is_active = true;
        $category2->fk_created_by = 1;
        $category2->save();

        $category3 = new CourseCategory();
        $category3->name = "Category 3";
        $category3->description = "Test description 3";
        $category3->image_path = "public/2021/April/";
        $category3->image_name = "06-46-30-608661c67787a-AdminLTELogo.png";
        $category3->is_active = true;
        $category3->fk_created_by = 1;
        $category3->save();

        $category4 = new CourseCategory();
        $category4->name = "Category 4";
        $category4->description = "Test description 4";
        $category4->image_path = "public/2021/April/";
        $category4->image_name = "06-46-30-608661c67787a-AdminLTELogo.png";
        $category4->is_active = true;
        $category4->fk_created_by = 1;
        $category4->save();

        $course1 = new Course();
        $course1->category()->associate($category1);
        $course1->short_name = "course 1";
        $course1->name = "course 1";
        $course1->is_active = true;
        $course1->createdBy()->associate($user);
        $course1->save();

        $course2 = new Course();
        $course2->category()->associate($category1);
        $course2->short_name = "course 2";
        $course2->name = "course 2";
        $course2->is_active = true;
        $course2->createdBy()->associate($user);
        $course2->save();

        $course3 = new Course();
        $course3->category()->associate($category2);
        $course3->short_name = "course 3";
        $course3->name = "course 3";
        $course3->is_active = true;
        $course3->createdBy()->associate($user);
        $course3->save();

        $course4 = new Course();
        $course4->category()->associate($category2);
        $course4->short_name = "course 4";
        $course4->name = "course 4";
        $course4->is_active = true;
        $course4->createdBy()->associate($user);
        $course4->save();

        $course5 = new Course();
        $course5->category()->associate($category3);
        $course5->short_name = "course 5";
        $course5->name = "course 5";
        $course5->is_active = true;
        $course5->createdBy()->associate($user);
        $course5->save();

        $course1->users()->attach($user1);
        $course1->users()->attach($user2);
        $course2->users()->attach($user3);
        $course2->users()->attach($user4);
        $course3->users()->attach($user1);
        $course3->users()->attach($user2);

    }
}
