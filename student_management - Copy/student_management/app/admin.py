from django.contrib import admin
from .models import *
from django.contrib.auth.admin import  UserAdmin
# Register your models here.


# modify the default user admin properties such as display,
#class UserModel(UserAdmin):
    #list_display = ['username', 'user_type']

admin.site.register(CustomUser)
admin.site.register(Session)
admin.site.register(Course)
admin.site.register(Student)
admin.site.register(Inscription)
admin.site.register(Program)
admin.site.register(Membership)
admin.site.register(Registration)
admin.site.register(Payment)


