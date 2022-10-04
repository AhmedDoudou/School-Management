from django.contrib import admin
from django.urls import path
from .views import (
    StudentCreateView,
    StudentListView,
    StudentUpdateView,
    StudentDeleteView,
    StudentDetailView,
    FormWizardView,
    render_pdf_view,
    student_render_pdf_view 
)



app_name = "student"
urlpatterns = [
    
    path("add/", StudentCreateView.as_view(), name="create"),
    path("inscription/", FormWizardView.as_view(), name="inscription"),
    path("list/", StudentListView.as_view(), name="list"),
    path("update/<int:pk>/", StudentUpdateView.as_view(), name="update"),
    path("detail/<int:pk>/", StudentDetailView.as_view(), name="detail"),
    path("delete/<int:pk>/", StudentDeleteView.as_view(), name="delete"),
    path("pdf/<int:pk>/", student_render_pdf_view, name="student_pdf"),



]