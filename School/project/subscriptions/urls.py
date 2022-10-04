from django.contrib import admin
from django.urls import path
from .views import SubscriptionCreateView, SubscriptionListView,SubscriptionUpdateView,SubscriptionDeleteView

app_name = "subscription"
urlpatterns = [
    
    path("add/", SubscriptionCreateView.as_view(), name="create"),
    path("list/", SubscriptionListView.as_view(), name="list"),
    path("update/<int:pk>/", SubscriptionUpdateView.as_view(), name="update"),
    path("delete/<int:pk>/", SubscriptionDeleteView.as_view(), name="delete"),


]