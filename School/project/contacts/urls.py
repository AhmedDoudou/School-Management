from django.urls import path
from .views import ContactCreateView, ContactListView,ContactUpdateView,ContactDeleteView
from contacts import views


app_name = "contact"
urlpatterns = [
    path("", ContactListView.as_view(), name="list"),
    path("add/", ContactCreateView.as_view(), name="create"),
    path("update/<int:pk>/", ContactUpdateView.as_view(), name="update"),
    path("delete/<int:pk>/", ContactDeleteView.as_view(), name="delete"),
   



]