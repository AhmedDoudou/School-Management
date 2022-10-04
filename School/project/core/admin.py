from django.contrib import admin
from .models import *

admin.site.register(Inscription)
admin.site.register(Subscription)
admin.site.register(Contacts)
admin.site.register(Programs)
admin.site.register(Student_Parent)

@admin.register(Student)
class StudentAdmin(admin.ModelAdmin):
    list_display = ("id","last_name", "first_name")

