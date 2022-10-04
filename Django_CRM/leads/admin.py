from django.contrib import admin

from .models import User, Agent, Lead, UserProfile, Category, FollowUp

admin.site.register(User)
admin.site.register(UserProfile)
admin.site.register(Agent)
admin.site.register(Lead)
admin.site.register(Category)
admin.site.register(FollowUp)

class LeadAdmin(admin.ModelAdmin):
    list_display = ('first_name', 'category', 'agent')

