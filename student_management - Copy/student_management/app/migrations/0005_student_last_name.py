# Generated by Django 4.0.4 on 2022-04-28 01:09

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('app', '0004_student_inscription'),
    ]

    operations = [
        migrations.AddField(
            model_name='student',
            name='last_name',
            field=models.CharField(blank=True, max_length=50),
        ),
    ]
