import 'package:flutter/material.dart';
import 'package:lachaudiere_app/models/categorie.dart';
import 'package:lachaudiere_app/models/evenement.dart';
import 'package:lachaudiere_app/services/api_service.dart';

// j'ai mis un enum pour que ça soit plus simple pour le tri mais pas obligatoire
enum SortOption {
  title,
  dateAsc,
  dateDesc,
  category,
}

class EvenementProvider extends ChangeNotifier {
  final ApiService _apiService;
  String _searchQuery = '';
  Categorie? _selectedCategory;
  SortOption _currentSortOption = SortOption.title;

  List<Evenement> _evenements = [];
  List<Categorie> _categories = [];
  bool _isLoading = false;
  String? _errorMessage;

  List<Categorie> get categories => _categories;
  Categorie? get selectedCategory => _selectedCategory;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;


  List<Evenement> get evenements {
    List<Evenement> processedEvents = List.from(_evenements);

    if (_searchQuery.isNotEmpty) {
      processedEvents = processedEvents
          .where((e) => e.titre.toLowerCase().contains(_searchQuery.toLowerCase()))
          .toList();
    }

    if (_selectedCategory != null) {
      processedEvents = processedEvents
          .where((e) => e.categorie.id == _selectedCategory!.id)
          .toList();
    }

    switch (_currentSortOption) {
      case SortOption.title:
        processedEvents.sort((a, b) => a.titre.toLowerCase().compareTo(b.titre.toLowerCase()));
        break;
      case SortOption.dateAsc:
        processedEvents.sort((a, b) => a.dateDebut.compareTo(b.dateDebut));
        break;
      case SortOption.dateDesc:
        processedEvents.sort((a, b) => b.dateDebut.compareTo(a.dateDebut));
        break;
      case SortOption.category:
        processedEvents.sort((a, b) => a.categorie.libelle.toLowerCase().compareTo(b.categorie.libelle.toLowerCase()));
        break;
    }

    return processedEvents;
  }

  EvenementProvider({required ApiService apiService}) : _apiService = apiService {
    _initialLoad();
  }

  void updateFilter(Categorie? category) {
    _selectedCategory = category;
    notifyListeners();
  }

  void updateSort(SortOption option) {
    _currentSortOption = option;
    notifyListeners(); 
  }

  
  void updateSearchQuery(String query) {
    _searchQuery = query;
    notifyListeners();
  }

  Future<void> refreshData() async {
    await _initialLoad();
  }
  
  Future<void> _initialLoad() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final results = await Future.wait([
        _apiService.fetchEvenements(),
        _apiService.fetchCategories(),
      ]);
      _evenements = results[0] as List<Evenement>;
      _categories = results[1] as List<Categorie>;
    } catch (error) {
      _errorMessage = "Erreur de chargement : $error";
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchAndApplyDetails(Evenement evenement) async {
    try {
      final detailsJson = await _apiService.fetchEvenementDetails(evenement.id);
      
      final domainUrl = _apiService.domainUrl;
      evenement.updateWithDetails(detailsJson, domainUrl);
      
      notifyListeners();
    } catch (e) {
      print("Erreur de chargement des détails pour l'événement ${evenement.id}: $e");
    }
  }
}