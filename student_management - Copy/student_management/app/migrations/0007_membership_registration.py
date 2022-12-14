# Generated by Django 4.0.4 on 2022-04-30 23:21

from django.db import migrations, models
import django.db.models.deletion


class Migration(migrations.Migration):

    dependencies = [
        ('app', '0006_program'),
    ]

    operations = [
        migrations.CreateModel(
            name='Membership',
            fields=[
                ('id', models.BigAutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('label', models.CharField(max_length=100)),
                ('payment_method', models.CharField(choices=[('CASH', 'CASH'), ('CHEQUE', 'CHEQUE'), ('CREDIT CARD', 'Bank Credit Card')], default='CASH', max_length=50)),
                ('membership_period', models.CharField(choices=[('M', 'MONTHLY'), ('T', 'TRIMESTRIAL'), ('A', 'ANNUAL')], default='T', max_length=50)),
            ],
        ),
        migrations.CreateModel(
            name='Registration',
            fields=[
                ('id', models.BigAutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('created_at', models.DateTimeField(auto_now_add=True)),
                ('updated_at', models.DateTimeField(auto_now=True)),
                ('membership', models.ForeignKey(on_delete=django.db.models.deletion.CASCADE, to='app.membership')),
                ('program', models.ForeignKey(on_delete=django.db.models.deletion.CASCADE, to='app.program')),
                ('student', models.ForeignKey(on_delete=django.db.models.deletion.CASCADE, to='app.student')),
            ],
        ),
    ]
