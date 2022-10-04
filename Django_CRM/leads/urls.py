from django.contrib import admin
from django.urls import path
from .views import (
    LeadDeleteView,
    LeadUpdateView, 
    LeadListView, 
    LeadDetailView, 
    LeadCreateView,
    AssignAgentView, 
    CategoryListView, 
    CategoryDetailView,
    FollowUpCreateView,
    FollowUpUpdateView,
    FollowUpDeleteView
)

app_name = "leads"

urlpatterns = [
    path('',LeadListView.as_view(), name='home'),
    path('create/',LeadCreateView.as_view(), name='create-lead'),
    path('detail/<int:pk>/',LeadDetailView.as_view(), name='detail-lead'),
    path('delete/<int:pk>/',LeadDeleteView.as_view(), name='delete-lead'),
    path('update/<int:pk>/',LeadUpdateView.as_view(), name='update-lead'),
    path('assign/<int:pk>/',AssignAgentView.as_view(), name='assign-agent'),
    path('categories/',CategoryListView.as_view(), name='category-list'),
    path('categories/<int:pk>/',CategoryDetailView.as_view(), name='category-detail'),
    path('followups/create/<int:pk>/',FollowUpCreateView.as_view(), name='lead-create-followup'),
    path('followups/update/<int:pk>/',FollowUpUpdateView.as_view(), name='lead-update-followup'),
    path('followups/delete/<int:pk>/',FollowUpDeleteView.as_view(), name='lead-delete-followup')






   
]
 