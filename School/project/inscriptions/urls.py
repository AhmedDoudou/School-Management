from django.contrib import admin
from django.urls import path
from .views import InscriptionCreateView, InscriptionListView,InscriptionUpdateView,InscriptionDeleteView
from inscriptions import views

app_name = "inscription"
urlpatterns = [
    
    path("add/", InscriptionCreateView.as_view(), name="create"),
    path("list/", InscriptionListView.as_view(), name="list"),
    path("update/<int:pk>/", InscriptionUpdateView.as_view(), name="update"),
    path("delete/<int:pk>/", InscriptionDeleteView.as_view(), name="delete"),
    # path("insc/",views. InscView, name="insc"),



]