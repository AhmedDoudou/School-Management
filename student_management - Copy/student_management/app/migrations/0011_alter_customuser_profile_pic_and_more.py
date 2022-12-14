# Generated by Django 4.0.4 on 2022-05-11 21:28

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('app', '0010_alter_student_grade_payment'),
    ]

    operations = [
        migrations.AlterField(
            model_name='customuser',
            name='profile_pic',
            field=models.ImageField(upload_to='profile_pic/'),
        ),
        migrations.AlterField(
            model_name='student',
            name='profile_pic',
            field=models.ImageField(blank=True, upload_to='medias/profile_pic/'),
        ),
    ]
