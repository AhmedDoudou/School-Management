from django.contrib import admin
from django.urls import path
from .views import AgentListView,AgentCreateView, AgentDetailView, AgentUpdateView,AgentDeleteView

app_name = "agents"

urlpatterns = [
    path('',AgentListView.as_view(), name='agent'),
    path('create/',AgentCreateView.as_view(), name='create-agent'),
    path('detail/<int:pk>/',AgentDetailView.as_view(), name='detail-agent'),
    path('delete/<int:pk>/',AgentDeleteView.as_view(), name='delete-agent'),
    path('update/<int:pk>/',AgentUpdateView.as_view(), name='update-agent'),
   
]
 