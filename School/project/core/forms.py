from django import forms
from crispy_forms.helper import FormHelper
from crispy_forms.layout import Submit
from django.conf import settings
from inscriptions.models import *


class StudentForm(forms.ModelForm):
   class Meta:
        model = Student
        fields = '__all__'
class Student_PrentForm(forms.ModelForm):
   class Meta:
        model = Student
        fields = ('__all__')


class ParentForm(forms.ModelForm):
   class Meta:
        model = Parent
        fields = ('__all__')

class InscriptionForm(forms.ModelForm):
   class Meta:
        model = Inscription
        fields = ('__all__')

class ProgramForm(forms.ModelForm):
   class Meta:
        model = Program
        fields = ('__all__')

class PaymentForm(forms.ModelForm):
   class Meta:
        model = Payment
        fields = ('__all__')

class RegistrationForm(forms.ModelForm):
   class Meta:
        model = Registration
        fields = ('__all__')

class MemberShipForm(forms.ModelForm):
   class Meta:
        model = Membership
        fields = ('__all__')

class InscriptionForm(forms.ModelForm):
   class Meta:
        model = Inscription
        fields = ('program','MemberShip')

