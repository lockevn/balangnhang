using System;
namespace Quantum.Tala.Service
{
    public interface ITournamentService
    {
        int CreateSoi(Quantum.Tala.Service.DTO.soiDTO p_dto);
        int CreateTournament(Quantum.Tala.Service.DTO.tournamentDTO p_dto);
        Quantum.Tala.Service.DTO.tournamentDTO[] GetTournamentList();
        Quantum.Tala.Service.DTO.tournamentDTO[] GetTournamentOfUser(string username);
    }
}
